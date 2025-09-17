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
                            :key="notification.key"
                        >
                            <td class="text-left">{{ notification.title }}</td>
                            <td class="text-left">
                                <select
                                    v-model="notification.channel"
                                    name="channel"
                                >
                                    <option
                                        v-for="channel in channels"
                                        :value="channel.key"
                                    >
                                        # {{ channel.name }}
                                    </option>
                                </select>
                            </td>

                            <td class="text-center">
                                <v-btn
                                    :loading="sendingKey === notification.key"
                                    :disabled="
                                        sendingKey &&
                                        sendingKey !== notification.key
                                    "
                                    density="comfortable"
                                    icon="mdi-send"
                                    color="primary"
                                    @click="
                                        sendNotification(
                                            notification.key,
                                            notification.channel,
                                        )
                                    "
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
import { ref, onMounted } from "vue";
import axios from "axios";
import { Notification, Channel } from "./types";

const sendingKey = ref<string | null>(null);

const notifications = ref<Notification[]>([]);
const channels = ref<Channel[]>([]);

const fetchNotifications = async () => {
    const response = await axios.get<Notification[]>("/api/notifications");
    notifications.value = response.data;
};

const fetchChannels = async () => {
    const response = await axios.get<Channel[]>("/api/channels");
    channels.value = response.data;
};

const sendNotification = async (key: string, channel: string) => {
    sendingKey.value = key;
    try {
        await axios.post(`/api/notifications`, { key, channel });
    } catch (error) {
        console.error(error);
    } finally {
        sendingKey.value = null;
    }
};

onMounted(() => {
    fetchNotifications();
    fetchChannels();
});
</script>

<style scoped lang="scss">
select {
    width: 100%;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 8px;
    font-size: 14px;
    color: #333;
    background-color: #fff;

    &:after {
        content: "▼";
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }
    option {
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 4px;
        font-size: 14px;
        color: #333;
        background-color: #fff;

        &:hover {
            background-color: #f0f0f0;
        }
        &:checked {
            background-color: #f0f0f0;
        }
        &:focus {
            background-color: #f0f0f0;
        }
    }
}
</style>
