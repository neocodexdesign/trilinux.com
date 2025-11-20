# 📖 Guia: Como Adicionar Tenants Automaticamente

## 🎯 Como o Sistema Funciona

### Fluxo Automático de Tenancy

```
┌─────────────────┐
│ Usuário acessa  │
│ trilinux.online │
└────────┬────────┘
         │
         ▼
┌─────────────────────────────┐
│ Middleware                  │
│ ConditionalTenancy          │
│                             │
│ 1. Pega o domínio           │
│ 2. Verifica se é central    │
│ 3. Se não, busca tenant     │
└────────┬────────────────────┘
         │
         ▼
┌─────────────────────────────┐
│ Tabela: domains             │
│                             │
│ domain='trilinux.online'    │
│ tenant_id='online'          │
└────────┬────────────────────┘
         │
         ▼
┌─────────────────────────────┐
│ Inicializa Tenant           │
│                             │
│ - Conecta ao banco:         │
│   tenant_online_todo        │
│ - Todas queries vão para lá │
└─────────────────────────────┘
```

---

## 🔧 Configuração Atual

### Domínio Central (NÃO usa tenancy)
- **Domínio:** `trilinux.com`
- **Banco:** `neocodexlabs_todo` (principal)
- **Usado para:** Login, Admin Filament, Registro

### Tenants Configurados

| Tenant ID | Domínio          | Banco de Dados         |
|-----------|------------------|------------------------|
| central   | trilinux.com     | tenant_central_todo    |
| online    | trilinux.online  | tenant_online_todo     |

⚠️ **PROBLEMA:** `trilinux.com` está DUPLICADO (central + tenant)

---

## ✅ Escolha UMA das Opções Abaixo

### **OPÇÃO 1: Sem Domínio Central (100% Multi-Tenant)**

Todos os domínios, incluindo `trilinux.com`, usam tenancy.

**Vantagens:**
- Isolamento total de dados
- Mais seguro
- Escalável

**Configurar:**

1. Edite `.env`:
```env
CENTRAL_DOMAIN=
```

2. Todos os domínios agora precisam estar na tabela `domains`

**Acesso ao Admin Filament:**
- Configure um domínio específico para admin (ex: `admin.trilinux.com`)

---

### **OPÇÃO 2: Com Domínio Central (Recomendado)**

`trilinux.com` = Admin central
Outros domínios = Tenants separados

**Vantagens:**
- Fácil gerenciar todos os tenants
- Admin centralizado

**Configurar:**

1. Manter `.env`:
```env
CENTRAL_DOMAIN=trilinux.com
```

2. **REMOVER** `trilinux.com` da tabela domains:
```bash
php artisan tinker --execute="
\App\Models\Domain::where('domain', 'trilinux.com')->delete();
echo 'Domínio central removido da tabela domains';
"
```

3. Configuração final:
- `trilinux.com` → Banco principal (admin, login)
- `trilinux.online` → Tenant 'online' (dados isolados)
- `cliente1.com` → Tenant 'cliente1' (criar quando precisar)

---

## 🚀 Como Adicionar NOVOS Tenants

### Método 1: Via Tinker (Manual)

```bash
php artisan tinker
```

```php
use App\Models\Tenant;
use App\Models\Domain;

// 1. Criar tenant
$tenant = Tenant::create([
    'id' => 'cliente1', // ID único
]);

// 2. Adicionar domínio
Domain::create([
    'domain' => 'cliente1.trilinux.com', // ou 'cliente1.com'
    'tenant_id' => 'cliente1',
]);

// 3. Criar banco e rodar migrations
// (isso é feito automaticamente quando criado via evento)
// Ou manualmente:
php artisan tenants:migrate --tenants=cliente1
```

---

### Método 2: Via Filament Admin (Automático)

1. Acesse `/admin` (domínio central)
2. Vá em "Tenants"
3. Clique "Novo Tenant"
4. Preencha:
   - **ID:** `cliente2`
   - **Nome:** `Cliente 2 Ltda`
   - **Domínio:** `cliente2.trilinux.com`
5. Salvar

✅ **Automático:**
- Cria registro em `tenants`
- Cria banco `tenant_cliente2_todo`
- Roda migrations
- Adiciona domínio

---

### Método 3: Via API (Programático)

```php
use App\Models\Tenant;
use App\Models\Domain;

// Em um controller/service
public function criarTenant($dados)
{
    // 1. Criar tenant
    $tenant = Tenant::create([
        'id' => $dados['slug'],
        'data' => [
            'name' => $dados['name'],
            'email' => $dados['email'],
            // ... outros dados customizados
        ]
    ]);

    // 2. Adicionar domínio
    $tenant->domains()->create([
        'domain' => $dados['domain'],
    ]);

    // 3. Criar usuário admin do tenant
    $tenant->run(function() use ($dados) {
        User::create([
            'name' => $dados['admin_name'],
            'email' => $dados['admin_email'],
            'password' => bcrypt($dados['password']),
            'role' => 'admin',
        ]);
    });

    return $tenant;
}
```

---

## 📊 Dados que Você DEVE Adicionar

### Para CADA Tenant:

1. **Obrigatório:**
   - `id` (string, único) - Identificador do tenant
   - `domain` - Domínio vinculado

2. **Opcional (no campo `data`):**
   - `name` - Nome da empresa
   - `slug` - URL amigável
   - `description` - Descrição
   - `is_active` - Ativo/Inativo
   - Qualquer outro dado customizado (JSON)

### Exemplo Completo:

```php
Tenant::create([
    'id' => 'acme-corp',
    'data' => [
        'name' => 'ACME Corporation',
        'slug' => 'acme',
        'description' => 'Empresa de tecnologia',
        'is_active' => true,
        'settings' => [
            'timezone' => 'America/Sao_Paulo',
            'language' => 'pt_BR',
            'max_users' => 50,
        ]
    ]
]);

Domain::create([
    'domain' => 'acme.trilinux.com',
    'tenant_id' => 'acme-corp',
]);
```

---

## 🎯 Próximos Passos Recomendados

1. **Escolher Opção 1 ou 2** (acima)
2. **Criar Resource Filament** para gerenciar tenants via admin
3. **Configurar DNS** para os domínios dos tenants
4. **Configurar envio de email** (cada tenant pode ter seu próprio SMTP)
5. **Criar seeder** para dados iniciais de cada tenant

---

## 🔍 Verificar Configuração Atual

```bash
php artisan tinker --execute="
use App\Models\Tenant;
use App\Models\Domain;

echo 'Tenants: ' . Tenant::count() . PHP_EOL;
echo 'Domains: ' . Domain::count() . PHP_EOL;
echo PHP_EOL;

foreach (Tenant::with('domains')->get() as \$t) {
    echo 'Tenant: ' . \$t->id . PHP_EOL;
    echo '  Banco: ' . \$t->getDatabaseName() . PHP_EOL;
    echo '  Domínios:' . PHP_EOL;
    foreach (\$t->domains as \$d) {
        echo '    - ' . \$d->domain . PHP_EOL;
    }
    echo PHP_EOL;
}
"
```

---

## ⚡ Comandos Úteis

```bash
# Listar todos os tenants
php artisan tenants:list

# Rodar migrations em todos os tenants
php artisan tenants:migrate

# Rodar migrations em tenant específico
php artisan tenants:migrate --tenants=cliente1

# Rodar seeder em tenant específico
php artisan tenants:seed --tenants=cliente1

# Executar comando em contexto de tenant
php artisan tenants:run cliente1 -- db:seed
```

---

## 🔒 Segurança

- ✅ Cada tenant tem banco separado
- ✅ Dados completamente isolados
- ✅ Um tenant NÃO pode acessar dados de outro
- ✅ Migrations rodam separadamente por tenant
- ✅ Cache separado por tenant

---

## 📞 Dúvidas?

- Documentação: https://tenancyforlaravel.com
- Package: stancl/tenancy v3.9
