<template>
    <v-layout>
        <Header />
        <Sidebar />
        <v-main>
            <v-container class="pa-4">
                <router-view />
            </v-container>
        </v-main>
    </v-layout>
</template>

<script setup lang="ts">
import Sidebar from "./components/Common/Sidebar.vue";
import Header from "./components/Common/Header.vue";
import { useGlobalProvider, globalKey } from "@/provider";
import { provide, onMounted } from "vue";

const provider = useGlobalProvider();
provide(globalKey, provider);

const { fetchErrorCount, fetchSDCount, fetchSDList } = provider;

onMounted(() => {
    fetchErrorCount();
    fetchSDCount();
    fetchSDList();
});
</script>

<style lang="scss" scoped>
.main {
    box-sizing: border-box;
    padding: 80px 20px 20px 220px;
    height: calc(100% - 60px);
    overflow-y: auto;
}
</style>
