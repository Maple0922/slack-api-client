<template>
    <Head title="メンバー" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                手動通知
            </h2>
        </template>
        <template #default> 手動通知 </template>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref, shallowRef, computed, reactive } from "vue";
import axios from "axios";
import { deepCopy } from "@/utils/deepCopy";
</script>
