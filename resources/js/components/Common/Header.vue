<template>
    <v-app-bar theme="dark">
        <template #prepend>
            <v-app-bar-nav-icon @click="toggleNavigation"></v-app-bar-nav-icon>
        </template>
        <v-app-bar-title class="text-h5 font-weight-bold">
            Slack API Client
        </v-app-bar-title>
    </v-app-bar>
</template>

<script setup lang="ts">
import { globalKey } from "@/provider";
import { strictInject } from "@/utils/strictInject";

const { isOpenNavigation } = strictInject(globalKey);

const toggleNavigation = () => {
    isOpenNavigation.value = !isOpenNavigation.value;
};
</script>
