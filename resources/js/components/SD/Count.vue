<template>
    <EasyDataTable
        :headers="headers"
        :items="sdCounts"
        :loading="isSDCountLoading"
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

const { isSDCountLoading, sdCounts } = strictInject(globalKey);

const headers: Header[] = [
    { text: "開始", value: "start" },
    { text: "終了", value: "end" },
    { text: "件数", value: "count" },
];
</script>
