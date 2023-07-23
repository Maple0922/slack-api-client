<template>
    <EasyDataTable
        :headers="headers"
        :items="errorCounts"
        :loading="isErrorCountLoading"
        hide-footer
        border-cell
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
    </EasyDataTable>
</template>

<script setup lang="ts">
import type { Header } from "vue3-easy-data-table";

import { strictInject } from "@/utils/strictInject";
import { globalKey } from "@/provider";

const { isErrorCountLoading, errorCounts } = strictInject(globalKey);

const headers: Header[] = [
    { text: "開始", value: "start" },
    { text: "終了", value: "end" },
    { text: "SD Console", value: "crmAdmin" },
    { text: "ExpertCloud v1", value: "crmExpertV1" },
    { text: "ExpertCloud v2", value: "crmExpertV2" },
    { text: "LINE Bot", value: "crmBot" },
    { text: "マネーキャリア", value: "moneyCareer" },
    { text: "マーケットクラウド for EGS", value: "crmMarketHolderEgs" },
    {
        text: "マーケットクラウド for SENライフ",
        value: "crmMarketHolderSenlife",
    },
    { text: "合計", value: "total" },
];
</script>
