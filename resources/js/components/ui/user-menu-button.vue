<script setup lang="ts">
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { User } from "lucide-vue-next";

defineProps<{
  user: {
    name: string;
    email: string;
    role: string;
  };
}>();
</script>

<template>
  <div class="flex space-x-2 py-2 px-3 rounded-full hover:bg-gray-100 transition-colors">
    <Avatar class="h-8 w-8 cursor-pointer border border-gray-200 shadow-sm">
      <AvatarFallback class="bg-gradient-to-br from-cyan-50 to-cyan-100 text-cyan-700">
        <User class="h-4 w-4" />
      </AvatarFallback>
    </Avatar>
    <div class="hidden sm:block text-left">
      <p class="text-sm font-medium text-gray-700 leading-tight">{{ user.name }}</p>
      <p class="text-xs text-cyan-600">{{ user.role }}</p>
    </div>
  </div>
</template>