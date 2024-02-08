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
    { text: "マネーキャリア", value: "moneyCareer" },
    { text: "SDコンソール", value: "crmAdmin" },
    { text: "MCエキスパートクラウド", value: "crmExpert" },
    { text: "MCマーケットクラウド", value: "crmMarketHolder" },
    { text: "LINE Bot", value: "crmBot" },
    { text: "合計", value: "total" },
];
</script>
