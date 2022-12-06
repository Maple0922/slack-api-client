<template>
    <header class="header">
        <h1 class="header__title">Slack API Client</h1>
    </header>
</template>

<script setup lang="ts"></script>

<style lang="scss" scoped>
.header {
    background-color: #000916;
    color: white;
    border-bottom: 1px solid #e5e5e5;
    width: 100%;
    height: 60px;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;

    &__title {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 60px;
        margin: 0 0 0 20px;
    }
}
</style>
