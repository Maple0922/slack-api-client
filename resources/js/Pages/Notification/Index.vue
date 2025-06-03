<template>
    <Head title="メンバー" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                手動通知
            </h2>
        </template>
        <template #default>
            <v-sheet border rounded class="max-w-7xl mx-auto mt-8">
                <v-table fixedHeader height="78vh">
                    <thead>
                        <tr>
                            <th class="text-left">タイトル</th>
                            <th class="text-left">チャンネル</th>
                            <th class="text-center">送信</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="notification in notifications"
                            :key="notification.path"
                        >
                            <td class="text-left">{{ notification.title }}</td>
                            <td class="text-left">
                                <a
                                    :href="notification.channel.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    # {{ notification.channel.name }}
                                </a>
                            </td>

                            <td class="text-center">
                                <v-btn
                                    :loading="sendingId === notification.id"
                                    :disabled="
                                        sendingId &&
                                        sendingId !== notification.id
                                    "
                                    density="comfortable"
                                    icon="mdi-send"
                                    color="primary"
                                    @click="sendNotification(notification)"
                                />
                            </td>
                        </tr></tbody
                ></v-table>
            </v-sheet>
        </template>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import axios from "axios";
import { Notification } from "./types";

const sendingId = ref<number | null>(null);

const notifications = ref<Notification[]>([
    {
        id: 1,
        title: "開発ポイント進捗通知",
        path: "engineer_dev_point",
        channel: {
            name: "50_engineer_dev_point",
            url: "https://wizleap.slack.com/archives/C07DH9WFLV7",
        },
    },
    {
        id: 2,
        title: "ロードマップ進捗通知",
        path: "engineer_roadmap",
        channel: {
            name: "50_engineer_dev_point",
            url: "https://wizleap.slack.com/archives/C07DH9WFLV7",
        },
    },
]);

const sendNotification = async (notification: Notification) => {
    sendingId.value = notification.id;
    await axios.post(`/api/notification/${notification.path}`);
    sendingId.value = null;
};
</script>
