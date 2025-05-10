<script setup lang="ts">
import { Button } from "@/components/ui/button";

defineProps<{
  active: boolean;
  icon?: any;
  label: string;
  onClick: () => void;
}>();
</script>

<template>
  <Button
    @click="onClick"
    :variant="active ? 'default' : 'ghost'"
    :class="[
      'rounded-md font-medium',
      active
        ? 'bg-gradient-to-r from-cyan-600 to-cyan-700 hover:from-cyan-700 hover:to-cyan-800 text-white shadow-sm'
        : 'hover:text-cyan-600 hover:bg-cyan-50',
    ]"
  >
    <component :is="icon" v-if="icon" class="h-4 w-4 mr-1.5" />
    {{ label }}
  </Button>
</template>