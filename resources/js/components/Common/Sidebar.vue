<template>
    <v-navigation-drawer
        permanent
        theme="dark"
        :rail="!isOpenNavigation"
        :value="true"
    >
        <v-list nav>
            <v-list-item
                v-for="item in navigationItems"
                :key="item.value"
                :prepend-icon="item.icon"
                :title="item.title"
                :value="item.value"
                :to="item.path"
            ></v-list-item>
        </v-list>
    </v-navigation-drawer>
</template>

<script setup lang="ts">
import { globalKey } from "@/provider";
import { strictInject } from "@/utils/strictInject";

const { isOpenNavigation } = strictInject(globalKey);

const navigationItems = [
    {
        icon: "mdi-account",
        title: "ユーザー管理",
        value: "users",
        path: "/users",
    },
];
</script>

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
