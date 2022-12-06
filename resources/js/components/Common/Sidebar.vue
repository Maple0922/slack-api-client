<template>
    <aside class="sidebar">
        <ul class="sidebar__list">
            <li class="sidebar__list__item">
                <router-link class="sidebar__list__item__link" to="/crm"
                    >mc_sd_crm</router-link
                >
            </li>
            <li class="sidebar__list__item">
                <router-link class="sidebar__list__item__link" to="/error"
                    >error</router-link
                >
            </li>
        </ul>
    </aside>
</template>

<script setup lang="ts"></script>

<style lang="scss" scoped>
.sidebar {
    box-sizing: border-box;
    background-color: #fff;
    border-right: 1px solid #e5e5e5;
    height: calc(100% - 60px);
    left: 0;
    bottom: 0;
    position: fixed;
    width: 200px;

    &__list {
        list-style: none;
        width: 100%;
        margin: 0;
        padding: 0;
        &__item {
            transition: 0.3s;
            width: 100%;
            height: 60px;
            box-sizing: border-box;
            line-height: 60px;
            border-bottom: 1px solid #e5e5e5;
            margin: 0;
            cursor: pointer;
            &:hover {
                background-color: #e5e5e5;
            }

            &__link {
                padding: 0 20px;
                color: #000;
                text-decoration: none;
                width: 100%;
                height: 100%;
                display: block;
            }
        }
    }
}
</style>
