<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // aceitar candidatura: cancela as outras ativas e cria o estágio
        DB::unprepared(<<<'SQL'
        CREATE OR REPLACE FUNCTION fn_candidaturas_aceite()
        RETURNS TRIGGER
        LANGUAGE plpgsql
        SET search_path = public
        AS $$
        DECLARE
          v_novas INT[];
        BEGIN
          IF TG_OP = 'INSERT' THEN
            SELECT array_agg(n.id) INTO v_novas
            FROM novas n
            WHERE n.estado = 'aceite';
          ELSE
            SELECT array_agg(n.id) INTO v_novas
            FROM novas n
            JOIN antigas a ON a.id = n.id
            WHERE n.estado = 'aceite'
              AND a.estado <> 'aceite';
          END IF;

          IF v_novas IS NULL THEN
            RETURN NULL;
          END IF;

          IF EXISTS (
            SELECT 1 FROM candidaturas c
            WHERE c.id = ANY (v_novas)
            GROUP BY c.aluno_id
            HAVING COUNT(*) > 1
          ) THEN
            RAISE EXCEPTION 'Um aluno não pode aceitar duas candidaturas ao mesmo tempo.'
              USING ERRCODE = '50001';
          END IF;

          UPDATE candidaturas c
             SET estado = 'cancelada'
          FROM candidaturas n
          WHERE n.id = ANY (v_novas)
            AND n.aluno_id = c.aluno_id
            AND c.id <> n.id
            AND c.estado IN ('submetida','em_analise','aceite_empresa');

          -- se o monitor da oferta estiver desativado, o estágio nasce sem monitor
          INSERT INTO estagios (candidatura_id, aluno_id, monitor_id)
          SELECT c.id, c.aluno_id, CASE WHEN u.desativado_em IS NULL THEN m.user_id END
          FROM candidaturas c
          JOIN ofertas o        ON o.id = c.oferta_id
          LEFT JOIN monitores m ON m.user_id = o.monitor_id AND m.ativo
          LEFT JOIN users u     ON u.id = m.user_id
          WHERE c.id = ANY (v_novas);

          RETURN NULL;
        END;
        $$;

        CREATE TRIGGER trg_candidaturas_aceite_insert
        AFTER INSERT ON candidaturas
        REFERENCING NEW TABLE AS novas
        FOR EACH STATEMENT EXECUTE FUNCTION fn_candidaturas_aceite();

        CREATE TRIGGER trg_candidaturas_aceite_update
        AFTER UPDATE ON candidaturas
        REFERENCING OLD TABLE AS antigas NEW TABLE AS novas
        FOR EACH STATEMENT EXECUTE FUNCTION fn_candidaturas_aceite();
        SQL);

        // valida a candidatura e o monitor do estágio
        DB::unprepared(<<<'SQL'
        CREATE OR REPLACE FUNCTION fn_estagios_validar()
        RETURNS TRIGGER
        LANGUAGE plpgsql
        SET search_path = public
        AS $$
        BEGIN
          IF EXISTS (
            SELECT 1 FROM candidaturas c
            WHERE c.id = NEW.candidatura_id
              AND c.estado <> 'aceite'
          ) THEN
            RAISE EXCEPTION 'Um estágio só pode ser criado a partir de uma candidatura aceite pela empresa e pelo aluno.'
              USING ERRCODE = '50002';
          END IF;

          IF EXISTS (
            SELECT 1
            FROM candidaturas c
            JOIN ofertas o   ON o.id = c.oferta_id
            JOIN monitores m ON m.user_id = NEW.monitor_id
            WHERE c.id = NEW.candidatura_id
              AND m.empresa_id <> o.empresa_id
          ) THEN
            RAISE EXCEPTION 'O monitor do estágio tem de pertencer à empresa da oferta.'
              USING ERRCODE = '50004';
          END IF;

          RETURN NULL;
        END;
        $$;

        CREATE TRIGGER trg_estagios_validar
        AFTER INSERT OR UPDATE ON estagios
        FOR EACH ROW EXECUTE FUNCTION fn_estagios_validar();
        SQL);

        // histórico de monitores
        DB::unprepared(<<<'SQL'
        CREATE OR REPLACE FUNCTION fn_estagios_historico_monitor()
        RETURNS TRIGGER
        LANGUAGE plpgsql
        SET search_path = public
        AS $$
        DECLARE
          v_agora TIMESTAMP := clock_timestamp() AT TIME ZONE 'utc';
        BEGIN
          UPDATE estagio_monitores
             SET data_fim = v_agora
          WHERE estagio_id = NEW.id
            AND data_fim IS NULL
            AND monitor_id IS DISTINCT FROM NEW.monitor_id;

          IF NEW.monitor_id IS NOT NULL THEN
            INSERT INTO estagio_monitores (estagio_id, monitor_id, data_inicio)
            SELECT NEW.id, NEW.monitor_id, v_agora
            WHERE NOT EXISTS (
              SELECT 1 FROM estagio_monitores em
              WHERE em.estagio_id = NEW.id
                AND em.data_fim IS NULL
            );
          END IF;

          RETURN NULL;
        END;
        $$;

        CREATE TRIGGER trg_estagios_historico_monitor
        AFTER INSERT OR UPDATE ON estagios
        FOR EACH ROW EXECUTE FUNCTION fn_estagios_historico_monitor();
        SQL);

        // os utilizadores nunca se apagam
        DB::unprepared(<<<'SQL'
        CREATE OR REPLACE FUNCTION fn_users_impedir_delete()
        RETURNS TRIGGER
        LANGUAGE plpgsql
        SET search_path = public
        AS $$
        BEGIN
          RAISE EXCEPTION 'Os utilizadores não podem ser apagados, devem ser só desativados.'
            USING ERRCODE = '50005';
        END;
        $$;

        CREATE TRIGGER trg_users_impedir_delete
        BEFORE DELETE ON users
        FOR EACH ROW EXECUTE FUNCTION fn_users_impedir_delete();

        CREATE TRIGGER trg_users_impedir_truncate
        BEFORE TRUNCATE ON users
        FOR EACH STATEMENT EXECUTE FUNCTION fn_users_impedir_delete();
        SQL);

        // expira candidaturas sem resposta em 15 dias
        DB::unprepared(<<<'SQL'
        CREATE OR REPLACE PROCEDURE sp_expirar_candidaturas()
        LANGUAGE sql
        SET search_path = public
        AS $$
          UPDATE candidaturas
             SET estado = 'expirada'
          WHERE estado = 'aceite_empresa'
            AND prazo_resposta < (now() AT TIME ZONE 'utc');
        $$;
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared(<<<'SQL'
        DROP PROCEDURE IF EXISTS sp_expirar_candidaturas();
        DROP TRIGGER IF EXISTS trg_users_impedir_truncate ON users;
        DROP TRIGGER IF EXISTS trg_users_impedir_delete ON users;
        DROP TRIGGER IF EXISTS trg_estagios_historico_monitor ON estagios;
        DROP TRIGGER IF EXISTS trg_estagios_validar ON estagios;
        DROP TRIGGER IF EXISTS trg_candidaturas_aceite_update ON candidaturas;
        DROP TRIGGER IF EXISTS trg_candidaturas_aceite_insert ON candidaturas;
        DROP FUNCTION IF EXISTS fn_users_impedir_delete();
        DROP FUNCTION IF EXISTS fn_estagios_historico_monitor();
        DROP FUNCTION IF EXISTS fn_estagios_validar();
        DROP FUNCTION IF EXISTS fn_candidaturas_aceite();
        SQL);
    }
};
