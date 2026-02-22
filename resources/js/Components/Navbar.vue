<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline';

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <nav class="sticky top-0 z-50 flex items-center justify-between bg-[#f6f8f8]/80 backdrop-blur-md px-6 py-4 border-b border-[#4c9a93]/10">
        <Link :href="route('home')">
            <div class="flex items-center gap-2">
                <img src="/logo.png" alt="Personaility Logo" class="h-10 w-auto object-contain shrink-0" />
            </div>
        </Link>
        
        <div class="flex items-center gap-3">
            <template v-if="user">
                <div class="flex items-center gap-2">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-semibold text-[#0d1b1a] leading-tight">{{ user.name }}</p>
                    </div>
                    <Link :href="route('profile.edit')" class="w-8 h-8 rounded-full bg-[#40D5C8]/20 flex items-center justify-center flex-shrink-0 cursor-pointer hover:bg-[#40D5C8]/30 transition-colors">
                        <span class="text-xs font-bold text-[#4c9a93]">{{ user.name?.charAt(0)?.toUpperCase() }}</span>
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" class="text-rose-500 hover:bg-rose-50 hover:text-rose-700 p-1.5 rounded-full transition-colors ml-1 flex items-center justify-center" title="Keluar">
                        <ArrowRightOnRectangleIcon class="w-5 h-5" />
                    </Link>
                </div>
            </template>
            <template v-else>
                <Link :href="route('login')" class="bg-[#40D5C8] text-[#0d1b1a] text-sm font-bold px-4 py-2 rounded-2xl shadow-md shadow-[#40D5C8]/20 hover:scale-105 active:scale-95 transition-all">Login</Link>
            </template>
        </div>
    </nav>
</template>
