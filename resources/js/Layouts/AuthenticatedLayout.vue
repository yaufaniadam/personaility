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
import Navbar from '@/Components/Navbar.vue';

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
    <Navbar />


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
