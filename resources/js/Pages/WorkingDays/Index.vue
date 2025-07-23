<template>
    <Head title="メンバー" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                稼働日数
            </h2>
        </template>
        <template #default>
            <v-sheet border rounded class="max-w-7xl mx-auto mt-8 mb-24">
                <v-table density="compact" fixedHeader height="78vh">
                    <thead>
                        <tr>
                            <th class="text-left">
                                <v-row class="items-center">
                                    <v-col cols="auto">
                                        <v-btn
                                            density="compact"
                                            variant="text"
                                            icon="mdi-chevron-left"
                                            @click="() => shiftWorkingDays(-1)"
                                        />
                                    </v-col>
                                    <v-col cols="auto">
                                        <span class="text-lg">{{ month }}</span>
                                    </v-col>
                                    <v-col cols="auto">
                                        <v-btn
                                            density="compact"
                                            variant="text"
                                            icon="mdi-chevron-right"
                                            @click="() => shiftWorkingDays(1)"
                                        />
                                    </v-col>
                                </v-row>
                            </th>
                            <th
                                class="text-center"
                                v-for="member in validMembers"
                                :key="member.notionId"
                            >
                                <v-tooltip location="top">
                                    <template #activator="{ props }">
                                        <v-avatar
                                            v-bind="props"
                                            size="30"
                                            :image="member.imageUrl"
                                        />
                                    </template>
                                    <template #default>
                                        <div class="text-center">
                                            {{ member.name }}
                                        </div>
                                    </template>
                                </v-tooltip>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="workingDay in workingDays"
                            :key="workingDay.date"
                        >
                            <td
                                :class="
                                    workingDay.isSaturday
                                        ? 'text-blue-500'
                                        : workingDay.isSunday
                                          ? 'text-red-500'
                                          : ''
                                "
                            >
                                {{ workingDay.date }} ({{ workingDay.week }})
                            </td>
                            <td
                                v-for="member in validMembers"
                                :key="member.notionId"
                                class="text-center"
                            >
                                <template
                                    v-if="
                                        workingDay.isSaturday ||
                                        workingDay.isSunday
                                    "
                                    >ー</template
                                >
                                <v-btn
                                    v-else-if="
                                        workingDay.members
                                            .map((member) => member.id)
                                            .includes(member.notionId)
                                    "
                                    density="compact"
                                    icon="mdi-check"
                                    variant="elevated"
                                    color="primary"
                                    @click="
                                        deleteWorkingDay(
                                            workingDay.date,
                                            member.notionId,
                                        )
                                    "
                                />
                                <v-btn
                                    v-else
                                    density="compact"
                                    icon="mdi-close"
                                    variant="elevated"
                                    color="error"
                                    @click="
                                        createWorkingDay(
                                            workingDay.date,
                                            member.notionId,
                                        )
                                    "
                                />
                            </td>
                        </tr>
                    </tbody>
                </v-table>
            </v-sheet>
        </template>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref, computed } from "vue";
import axios from "axios";

import { WorkingDay } from "./types";
import { Member } from "../Member/types";

const month = ref(new Date().toISOString().slice(0, 7));
const workingDays = ref<WorkingDay[]>([]);
const members = ref<Member[]>([]);

const monthOffset = ref(0);

const fetchWorkingDays = async (monthOffset: number) => {
    const response = await axios.get(`/api/working_days/${monthOffset}`);
    month.value = response.data.month;
    workingDays.value = response.data.workingDays as WorkingDay[];
};

const shiftWorkingDays = (offset: number) => {
    monthOffset.value += offset;
    fetchWorkingDays(monthOffset.value);
};

const fetchMembers = async () => {
    const response = await axios.get("/api/members");
    members.value = response.data;
};

const validMembers = computed(() =>
    members.value.filter((member) => member.isValid),
);

const createWorkingDay = async (date: string, memberId: string) => {
    await axios.post("/api/working_days", { date, memberId });
    await fetchWorkingDays(monthOffset.value);
};

const deleteWorkingDay = async (date: string, memberId: string) => {
    await axios.delete(`/api/working_days/${date}/${memberId}`);
    await fetchWorkingDays(monthOffset.value);
};

onMounted(() => {
    fetchWorkingDays(monthOffset.value);
    fetchMembers();
});
</script>
