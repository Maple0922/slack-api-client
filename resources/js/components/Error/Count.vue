<template>
    <EasyDataTable
        :headers="headers"
        :items="errors"
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
interface Error extends Item {
    week: string;
    crmAdmin: number;
    crmExpert: number;
    crmBot: number;
    moneyCareer: number;
}

const errors: Ref<Error[]> = ref([]);

const isLoading = ref(false);

const fetchErrorCount = async () => {
    isLoading.value = true;
    const { data } = await axios.get<Error[]>("/api/errors/count");
    errors.value = data;
    isLoading.value = false;
};

const headers: Header[] = [
    { text: "週", value: "week" },
    { text: "SD Console", value: "crmAdmin" },
    { text: "専門家CRM", value: "crmExpert" },
    { text: "LINE Bot", value: "crmBot" },
    { text: "マネーキャリア", value: "moneyCareer" },
    { text: "合計", value: "total" },
];

onMounted(async () => {
    fetchErrorCount();
});
</script>
