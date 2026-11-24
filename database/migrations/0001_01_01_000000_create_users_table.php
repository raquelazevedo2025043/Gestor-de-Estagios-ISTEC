<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
        CREATE OR REPLACE FUNCTION fn_roles_validos(p_roles JSONB)
        RETURNS BOOLEAN
        LANGUAGE plpgsql
        IMMUTABLE
        SET search_path = ''
        AS $$
        BEGIN
          IF p_roles IS NULL OR jsonb_typeof(p_roles) <> 'array' OR jsonb_array_length(p_roles) = 0 THEN
            RETURN FALSE;
          END IF;

          RETURN (
            SELECT bool_and(
                     jsonb_typeof(r.valor) = 'string'
                     AND (r.valor #>> '{}') IN ('aluno', 'professor', 'monitor', 'empresa', 'coordenacao')
                   )
                   AND count(*) = count(DISTINCT r.valor)
            FROM jsonb_array_elements(p_roles) AS r(valor)
          );
        END;
        $$;
        SQL);

        Schema::create('users', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->string('nome', 150);
            $table->string('email', 150);
            $table->string('password');
            $table->rememberToken();
            $table->jsonb('roles');
            $table->timestamp('created_at', 6)->default(DB::raw("(now() at time zone 'utc')"));
            $table->timestamp('updated_at', 6)->nullable();
            $table->timestamp('desativado_em', 6)->nullable();
        });

        DB::unprepared(<<<'SQL'
        ALTER TABLE users ADD CONSTRAINT ck_users_roles CHECK (fn_roles_validos(roles));
        CREATE UNIQUE INDEX uq_users_email ON users (lower(email));
        SQL);

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->integer('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_roles_validos(JSONB);');
    }
};
