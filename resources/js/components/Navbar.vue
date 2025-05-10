<script setup lang="ts">
import { computed, ref } from "vue";
import { 
  DropdownMenu, 
  DropdownMenuTrigger, 
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator
} from "./ui/dropdown-menu";
import { Button } from "@/components/ui/button";
import { Avatar, AvatarFallback } from "./ui/avatar";
import { User, Menu, X, Home, FileText, Award, Settings, LogOut, UserCircle } from "lucide-vue-next";
import NavItem from "./ui/nav-item.vue";
import UserMenuButton from "./ui/user-menu-button.vue";
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const currentRoute = computed(() => page.url);

const mobileMenuOpen = ref<boolean>(false);

const toggleMobileMenu = (): void => {
  mobileMenuOpen.value = !mobileMenuOpen.value;
};

// Navigation items configuration
const navItems = [
  { id: 'home', label: 'Beranda', icon: Home, href: route('home') },
  { id: 'pendaftaran', label: 'Pendaftaran', icon: FileText, href: route('pendaftaran')  },
  { id: 'skema', label: 'Skema Sertifikasi', icon: Award, href: route('skema')  },
];

// Determine active item based on current route
const activeItem = computed(() => {
  const currentUrl = currentRoute.value;
  
  for (const item of navItems) {
    // Extract path from href for comparison - add safety checks
    try {
      if (item.href) {
        const itemPath = new URL(item.href).pathname;
        if (currentUrl === itemPath) {
          return item.id;
        }
      }
    } catch (error) {
      console.error(`Error parsing URL for ${item.id}:`, error);
    }
  }
  
  // Default to home if no match found
  return 'home';
});

// Close mobile menu when navigating
const setActive = (): void => {
  mobileMenuOpen.value = false;
};

// Dummy user data
const user = {
  name: "Ahmad Fauzi",
  email: "ahmad.fauzi@example.com",
  role: "Peserta"
};
</script>

<template>
    <header class="border-b shadow-sm bg-white sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <img src="/images/logo.png" alt="LSP Logo" class="h-10 md:h-12" />
                    <div class="hidden sm:block">
                        <span class="font-semibold text-lg text-gray-800">Lembaga Sertifikasi Profesi</span>
                        <p class="text-xs text-gray-500 -mt-1">Teknologi Digital</p>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <button @click="toggleMobileMenu" class="md:hidden p-1 rounded-md hover:bg-gray-100 transition-colors">
                    <Menu v-if="!mobileMenuOpen" class="h-6 w-6 text-gray-700" />
                    <X v-else class="h-6 w-6 text-gray-700" />
                </button>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <!-- Navigation Menu -->
                    <nav>
                        <ul class="flex space-x-1">
                            <li v-for="item in navItems" :key="item.id">
                                <Link :href="item.href">
                                    <NavItem 
                                        :active="activeItem === item.id"
                                        :icon="item.icon"
                                        :label="item.label"
                                        :onClick="() => setActive()"
                                    />
                                </Link>
                            </li>
                        </ul>
                    </nav>

                    <!-- User Menu -->
                    <DropdownMenu>
                        <DropdownMenuTrigger class="focus:outline-none">
                            <UserMenuButton :user="user" />
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56 p-1">
                            <div class="p-2 mb-1 bg-gray-50 rounded-md">
                                <p class="font-medium text-gray-800">{{ user.name }}</p>
                                <p class="text-sm text-gray-500">{{ user.email }}</p>
                                <p class="text-xs text-cyan-600 mt-1 font-medium">{{ user.role }}</p>
                            </div>
                            <Link :href="route('profil')">
                                <DropdownMenuItem class="cursor-pointer hover:bg-cyan-50 focus:bg-cyan-50 rounded-md mt-1">
                                    <UserCircle class="h-4 w-4 mr-2 text-gray-500" />
                                    Profil Saya
                                </DropdownMenuItem>
                            </Link>
                            <!-- <Link :href="route('settings')"> -->
                                <DropdownMenuItem class="cursor-pointer hover:bg-cyan-50 focus:bg-cyan-50 rounded-md">
                                    <Settings class="h-4 w-4 mr-2 text-gray-500" />
                                    Pengaturan
                                </DropdownMenuItem>
                            <!-- </Link> -->
                            <DropdownMenuSeparator />
                            <Link :href="route('logout')" method="post">
                                <DropdownMenuItem class="cursor-pointer hover:bg-red-50 focus:bg-red-50 text-red-600 rounded-md">
                                    <LogOut class="h-4 w-4 mr-2" />
                                    Keluar
                                </DropdownMenuItem>
                            </Link>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div v-if="mobileMenuOpen" class="md:hidden mt-4 pb-2 animate-in slide-in-from-top-5 duration-200">
                <nav class="bg-white rounded-lg p-3 shadow-sm border border-gray-100">
                    <ul class="flex flex-col space-y-2">
                        <li v-for="item in navItems" :key="item.id">
                            <Link :href="item.href">
                                <NavItem 
                                    :active="activeItem === item.id"
                                    :icon="item.icon"
                                    :label="item.label"
                                    :onClick="() => setActive()"
                                    class="w-full justify-start"
                                />
                            </Link>
                        </li>
                    </ul>
                </nav>
                
                <!-- Mobile User Info -->
                <div class="mt-4 pt-3 border-t border-gray-200">
                    <div class="flex items-center space-x-3 p-2">
                        <Avatar class="h-10 w-10 border border-gray-200 shadow-sm">
                            <AvatarFallback class="bg-gradient-to-br from-cyan-50 to-cyan-100 text-cyan-700">
                                <User class="h-5 w-5" />
                            </AvatarFallback>
                        </Avatar>
                        <div>
                            <p class="font-medium text-gray-800">{{ user.name }}</p>
                            <p class="text-xs text-cyan-600">{{ user.role }}</p>
                            <p class="text-xs text-gray-500">{{ user.email }}</p>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1 bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100">
                        <Link :href="route('profil')">
                            <Button variant="ghost" class="w-full justify-start text-gray-700 hover:bg-cyan-50 hover:text-cyan-600 px-4">
                                <UserCircle class="h-4 w-4 mr-2 text-gray-500" />
                                Profil Saya
                            </Button>
                        </Link>
                        <!-- <Link :href="route('settings')"> -->
                            <Button variant="ghost" class="w-full justify-start text-gray-700 hover:bg-cyan-50 hover:text-cyan-600 px-4">
                                <Settings class="h-4 w-4 mr-2 text-gray-500" />
                                Pengaturan
                            </Button>
                        <!-- </Link> -->
                        <div class="border-t border-gray-100 my-1"></div>
                        <Link :href="route('logout')" method="post">
                            <Button variant="ghost" class="w-full justify-start text-red-600 hover:bg-red-50 px-4">
                                <LogOut class="h-4 w-4 mr-2" />
                                Keluar
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>