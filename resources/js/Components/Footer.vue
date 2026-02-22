<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="px-6 py-12 text-center space-y-6 border-t border-slate-100">
        <div class="flex flex-col items-center gap-2">
            <img src="/logo.png" alt="Personaility Logo" class="h-12 w-auto object-contain shrink-0 mb-2" />
            <p class="text-sm text-slate-500 max-w-[200px]">Berbasis Big Five OCEAN &middot; Bukan alat diagnostik klinis</p>
        </div>
        <div class="flex justify-center gap-6 text-sm font-medium text-[#4c9a93]">
            <Link :href="route('privacy')" class="hover:underline">Privasi</Link>
            <Link :href="route('terms')" class="hover:underline">Ketentuan</Link>
            <Link :href="route('psychologists.index')" class="hover:underline">Direktori Psikolog</Link>
        </div>
        <p class="text-xs text-slate-400">© 2025 Personaility. All rights reserved.</p>
    </footer>
</template>
