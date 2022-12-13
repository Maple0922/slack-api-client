<template>
    <EasyDataTable
        :headers="headers"
        :items="sdList"
        :loading="isSDListLoading"
        :rows-per-page="100"
    >
        <template #loading>
            <div class="text-center">
                <img
                    src="../../../assets/party-parrot.gif"
                    style="width: 50px"
                />
                <p class="text-caption">Loading...</p>
            </div>
        </template>
        <template #item-user="{ user }">
            <div v-html="user" class="pa-2"></div>
        </template>
        <template #item-content="{ content }">
            <div v-html="content" class="pa-2"></div>
        </template>
    </EasyDataTable>
</template>

<script setup lang="ts">
import type { Header } from "vue3-easy-data-table";

import { strictInject } from "@/utils/strictInject";

import { globalKey } from "@/provider";

const { isSDListLoading, sdList } = strictInject(globalKey);

const headers: Header[] = [
    { text: "日付", value: "date", width: 100 },
    { text: "時間", value: "time", width: 80 },
    { text: "送信者", value: "user", width: 180 },
    { text: "内容", value: "content" },
];
</script>
