<template>
    <EasyDataTable
        :headers="headers"
        :items="sds"
        :loading="isLoading"
        hide-footer
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
import axios from "axios";
import { ref, Ref, onMounted } from "vue";

import type { Header, Item } from "vue3-easy-data-table";
interface SD extends Item {
    week: string;
    count: number;
}

const sds: Ref<SD[]> = ref([]);

const isLoading = ref(false);

const fetchSDCount = async () => {
    isLoading.value = true;
    const { data } = await axios.get<SD[]>("/api/sds/count");
    sds.value = data;
    isLoading.value = false;
};

const headers: Header[] = [
    { text: "週", value: "week" },
    { text: "件数", value: "count" },
];

onMounted(async () => {
    fetchSDCount();
});
</script>
