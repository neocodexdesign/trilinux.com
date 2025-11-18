<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Tenant;
use Stancl\Tenancy\Jobs\CreateDatabase;
use Stancl\Tenancy\Jobs\MigrateDatabase;

class TenantCreate extends Command
{
    protected $signature = 'tenant:create
        {--id= : ID único do tenant (ex: neocodexdesign)}
        {--domain= : Domínio do tenant (ex: neocodexdesign.com)}
        {--name= : Nome exibido (ex: Neocodex Design)}
        {--plan=pro : Plano (ex: free|pro)}
        {--timezone=America/New_York : Timezone}';

    protected $description = 'Cria um novo tenant (ID, domínio, DB, migrações).';

    public function handle(): int
    {
        if (tenant()) {
            $this->error('Execute este comando no app CENTRAL (fora de tenancy).');
            return self::FAILURE;
        }

        $id = $this->option('id') ?: $this->ask('Tenant ID (ex: neocodexdesign)');
        $domain = $this->option('domain') ?: $this->ask('Domínio (ex: neocodexdesign.com)');
        $name = $this->option('name') ?: $this->ask('Nome (ex: Neocodex Design)');
        $plan = $this->option('plan') ?: 'pro';
        $timezone = $this->option('timezone') ?: 'America/New_York';

        if (!$id || !$domain) {
            $this->error('ID e domain são obrigatórios.');
            return self::INVALID;
        }

        if (Tenant::find($id)) {
            $this->error("Já existe tenant com id={$id}");
            return self::INVALID;
        }

        // Força o ID sem depender de mass assignment
        $tenant = new Tenant();
        $tenant->id = $id;
        $tenant->data = ['name' => $name, 'plan' => $plan, 'timezone' => $timezone];
        $tenant->save();

        // Domínio
        $tenant->domains()->create(['domain' => $domain]);

        // Nome do DB baseado no ID (sem pontos/traços)
        $dbName = $this->computeDbNameFromId($tenant);
        $tenant->tenancy_db_name = $dbName;
        $tenant->save();

        // Cria DB somente se ainda não existir; sempre migra depois
        $mgr = $tenant->database()->manager();
        if (!$mgr->databaseExists($dbName)) {
            CreateDatabase::dispatchSync($tenant);
        } else {
            $this->warn("Database já existe: {$dbName} (pulando criação)");
        }

        MigrateDatabase::dispatchSync($tenant);

        $this->info("OK! Tenant {$id} criado.");
        $this->line("Domain:  {$domain}");
        $this->line("DB:      {$dbName}");
        return self::SUCCESS;
    }

    // Gera tenant_{id_sanitizado}_todo (ex.: id 'neocodexdesign' -> tenant_neocodexdesign_todo)
    protected function computeDbNameFromId(Tenant $tenant): string
    {
        // usa o tenant key (id) e sanitiza pra nome de DB
        $base = (string) $tenant->getTenantKey(); // o id que você passou
        $slug = Str::of($base)->lower()
            ->replace(['.', '-'], '_')
            ->replaceMatches('/[^a-z0-9_]+/', '_')
            ->trim('_');

        return "tenant_{$slug}_todo";
    }
}