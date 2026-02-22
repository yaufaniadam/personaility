<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import {
  HomeIcon,
  ClipboardDocumentListIcon,
  UserCircleIcon,
  SparklesIcon,
  Squares2X2Icon,
} from '@heroicons/vue/24/outline';
import {
  HomeIcon as HomeIconSolid,
  ClipboardDocumentListIcon as ClipboardSolid,
  UserCircleIcon as UserSolid,
  Squares2X2Icon as GridSolid,
} from '@heroicons/vue/24/solid';

const page = usePage();
const user = page.props.auth.user;

const navItems = [
  {
    label: 'Home',
    route: 'dashboard',
    icon: HomeIcon,
    iconActive: HomeIconSolid,
  },
  {
    label: 'Arsip',
    route: 'assessment.history',
    icon: ClipboardDocumentListIcon,
    iconActive: ClipboardSolid,
  },
];

function isActive(routeName) {
  return route().current(routeName) || route().current(routeName + '.*');
}
</script>

<template>
  <div class="min-h-screen bg-[#f6f8f8] font-sans">

    <!-- Top bar -->
    <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-100 px-5 py-3 flex items-center justify-between">
      <Link :href="route('dashboard')" class="flex items-center gap-2">
        <img src="/logo.png" alt="Personaility Logo" class="h-8 w-auto object-contain shrink-0" />
      </Link>

      <div class="flex items-center gap-2">
        <div class="text-right hidden sm:block">
          <p class="text-xs font-semibold text-[#0d1b1a] leading-tight">{{ user.name }}</p>
          <p class="text-[10px] text-slate-400 leading-tight">{{ user.email }}</p>
        </div>
        <Link :href="route('profile.edit')" class="w-8 h-8 rounded-full bg-[#40D5C8]/20 flex items-center justify-center flex-shrink-0 cursor-pointer hover:bg-[#40D5C8]/30 transition-colors">
          <span class="text-xs font-bold text-[#4c9a93]">{{ user.name?.charAt(0)?.toUpperCase() }}</span>
        </Link>
      </div>
    </header>

    <!-- Page content with bottom padding for nav bar -->
    <main class="pb-24">
      <slot />
    </main>

    <!-- Bottom navigation bar -->
    <nav class="fixed bottom-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-lg border-t border-slate-100 px-4 py-2 max-w-lg mx-auto">
      <div class="flex items-center justify-around">

        <!-- Left nav items -->
        <Link
          v-for="item in navItems.slice(0, 1)"
          :key="item.route"
          :href="route(item.route)"
          class="flex flex-col items-center gap-1 min-w-[60px] py-1 transition-colors"
          :class="isActive(item.route) ? 'text-[#40D5C8]' : 'text-slate-400'"
        >
          <component :is="isActive(item.route) ? item.iconActive : item.icon" class="w-6 h-6" />
          <span class="text-[10px] font-semibold">{{ item.label }}</span>
        </Link>

        <!-- Center FAB – Start Assessment -->
        <div class="relative -top-5">
          <Link
            :href="route('assessment.consent')"
            class="w-14 h-14 rounded-full bg-[#40D5C8] text-[#0d1b1a] shadow-lg shadow-[#40D5C8]/40 flex items-center justify-center border-4 border-[#f6f8f8] hover:scale-105 transition-transform"
          >
            <ClipboardDocumentListIcon class="w-7 h-7" />
          </Link>
          <span class="absolute -bottom-4 left-1/2 -translate-x-1/2 text-[9px] font-bold text-slate-400 whitespace-nowrap">Assessment</span>
        </div>

        <!-- Right nav items -->
        <Link
          v-for="item in navItems.slice(1)"
          :key="item.route"
          :href="route(item.route)"
          class="flex flex-col items-center gap-1 min-w-[60px] py-1 transition-colors"
          :class="isActive(item.route) ? 'text-[#40D5C8]' : 'text-slate-400'"
        >
          <component :is="isActive(item.route) ? item.iconActive : item.icon" class="w-6 h-6" />
          <span class="text-[10px] font-semibold">{{ item.label }}</span>
        </Link>

      </div>
    </nav>

  </div>
</template>
