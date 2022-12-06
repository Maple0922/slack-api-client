<template>
    <Header />
    <Sidebar />
    <main class="main">
        <router-view />
    </main>
</template>

<script setup lang="ts">
import Sidebar from "./components/Common/Sidebar.vue";
import Header from "./components/Common/Header.vue";
</script>

<style lang="scss" scoped>
.main {
    box-sizing: border-box;
    padding: 80px 20px 20px 220px;
    height: calc(100% - 60px);
    overflow-y: auto;
}
</style>
