<template>
    <EasyDataTable
        :headers="headers"
        :items="displayErrorList"
        :loading="isErrorListLoading"
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
        <template #item-content="{ content }">
            <div v-html="content" class="pa-3 ma-1 code"></div>
        </template>
    </EasyDataTable>
</template>

<script setup lang="ts">
import type { Header } from "vue3-easy-data-table";
import { computed } from "vue";
import { strictInject } from "@/utils/strictInject";

import { globalKey } from "@/provider";

const { isErrorListLoading, errorList } = strictInject(globalKey);

const headers: Header[] = [
    { text: "最新発生日時", value: "datetime", width: 100 },
    { text: "チャンネル", value: "channel", width: 200 },
    { text: "件数", value: "count", width: 60 },
    { text: "内容", value: "content" },
];

const channels = [
    { key: "crm-admin", label: "SD Console" },
    { key: "crm-expert_v1", label: "Expert Cloud v1" },
    { key: "crm-expert_v2", label: "Expert Cloud v2" },
    { key: "crm-bot", label: "LINE Bot" },
    { key: "crm-market-holder_egs", label: "Market Cloud for EGS" },
    {
        key: "crm-market-holder_senlife",
        label: "Market Cloud for SENライフ",
    },
    { key: "money-career", label: "マネーキャリア" },
];

const displayErrorList = computed(() => {
    return errorList.value.map((error) => {
        const channel = channels.find((c) => c.key === error.channel);
        return {
            ...error,
            channel: channel ? channel.label : error.channel,
        };
    });
});
</script>

<style>
.code {
    white-space: pre-wrap;
    font-family: "Source Code Pro", monospace;
    background-color: #e9e9e9;
    border-radius: 4px;
    word-break: break-word;
}
</style>
