<x-filament-panels::page.simple>
    <!-- Debug Info -->
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
        <p class="font-bold">Debug Info:</p>
        <ul class="text-xs mt-2 space-y-1">
            <li><strong>Tenant Initialized:</strong> {{ $debugInfo['tenant_initialized'] ? 'Yes' : 'No' }}</li>
            <li><strong>Tenant ID:</strong> {{ $debugInfo['current_tenant_id'] }}</li>
            <li><strong>Database:</strong> {{ $debugInfo['database_name'] }}</li>
            <li><strong>Connection:</strong> {{ $debugInfo['connection_name'] }}</li>
            <li><strong>Domain:</strong> {{ $debugInfo['domain'] }}</li>
            <li><strong>Guard:</strong> {{ $debugInfo['guard'] }}</li>
        </ul>
    </div>

    <!-- Form de Login Original -->
    {{ $this->form }}

    <x-filament-panels::form.actions
        :actions="$this->getCachedFormActions()"
        :full-width="$this->hasFullWidthFormActions()"
    />
</x-filament-panels::page.simple>