<template>
  <div>
    <div class="md:flex md:items-center md:justify-between mb-4">
      <div class="min-w-0 flex-1">
        <h4 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight mb-4">Ke zpracování</h4>
      </div>
    </div>

    <div class="mb-4 divide-y divide-gray-200 h-auto rounded border-gray-200 border">
      <nuxt-link href="/finance/inbox/expense_drafts">
        <div class="px-3 py-2 hover:bg-slate-50 text-gray-700 text-sm">
          Výdaje ke zpracování
        </div>
      </nuxt-link>

      <nuxt-link href="/finance/inbox/payments_to_pair">
        <div class="px-3 py-2 hover:bg-slate-50 text-gray-700 text-sm">
          Platby ke spárování
        </div>
      </nuxt-link>
    </div>

  </div>
</template>

<script>
export default {}

</script>