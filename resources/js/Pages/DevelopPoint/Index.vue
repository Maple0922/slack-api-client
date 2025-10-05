<template>
    <Head title="開発ポイント" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                開発ポイント
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
                                            @click="() => shiftMonth(-6)"
                                        />
                                    </v-col>
                                    <v-col cols="auto">
                                        <span class="text-lg"
                                            >{{
                                                developPointHistory.dateRange
                                                    .start
                                            }}
                                            ~
                                            {{
                                                developPointHistory.dateRange
                                                    .end
                                            }}</span
                                        >
                                    </v-col>
                                    <v-col cols="auto">
                                        <v-btn
                                            density="compact"
                                            variant="text"
                                            icon="mdi-chevron-right"
                                            @click="() => shiftMonth(6)"
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
                            v-for="developPoint in developPointHistory.points"
                            :key="developPoint.inReviewDate"
                        >
                            <td>
                                {{ developPoint.inReviewDate }}
                            </td>
                            <td
                                v-for="member in validMembers"
                                :key="member.notionId"
                                class="text-center"
                            >
                                <template
                                    v-if="
                                        developMember(
                                            member.notionId,
                                            developPoint,
                                        )
                                    "
                                >
                                    <p class="text-xl">
                                        {{
                                            Math.round(
                                                (developMember(
                                                    member.notionId,
                                                    developPoint,
                                                )?.point /
                                                    developMember(
                                                        member.notionId,
                                                        developPoint,
                                                    )?.target) *
                                                    100,
                                            )
                                        }}%
                                    </p>
                                    <p class="text-xs">
                                        ({{
                                            developMember(
                                                member.notionId,
                                                developPoint,
                                            )?.point
                                        }}/{{
                                            developMember(
                                                member.notionId,
                                                developPoint,
                                            )?.target
                                        }})
                                    </p>
                                </template>
                                <template v-else> - </template>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-lg">Total</td>
                            <td
                                v-for="member in validMembers"
                                :key="member.notionId"
                                class="text-center"
                            >
                                <template
                                    v-if="
                                        developPointHistory.totalPoints.find(
                                            (totalPoint) =>
                                                totalPoint.notionId ===
                                                member.notionId,
                                        ) !== undefined
                                    "
                                >
                                    <p class="text-xl">
                                        {{
                                            Math.round(
                                                ((developPointHistory.totalPoints.find(
                                                    (totalPoint) =>
                                                        totalPoint.notionId ===
                                                        member.notionId,
                                                )?.totalPoint || 0) /
                                                    (developPointHistory.totalPoints.find(
                                                        (totalPoint) =>
                                                            totalPoint.notionId ===
                                                            member.notionId,
                                                    )?.totalTarget || 1)) *
                                                    100,
                                            )
                                        }}%
                                    </p>
                                    <p class="text-xs">
                                        ({{
                                            developPointHistory.totalPoints.find(
                                                (totalPoint) =>
                                                    totalPoint.notionId ===
                                                    member.notionId,
                                            )?.totalPoint
                                        }}/{{
                                            developPointHistory.totalPoints.find(
                                                (totalPoint) =>
                                                    totalPoint.notionId ===
                                                    member.notionId,
                                            )?.totalTarget
                                        }})
                                    </p>
                                </template>
                                <template v-else> - </template>
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
import { onMounted, ref, computed, reactive } from "vue";
import axios from "axios";

import {
    DevelopPoint,
    DevelopPointMember,
    DevelopPointHistory,
    DevelopPointDateRange,
} from "./types";
import { Member } from "../Member/types";

const members = ref<Member[]>([]);

const developPointHistory = ref<DevelopPointHistory>({
    dateRange: {
        start: "",
        end: "",
    },
    points: [],
    totalPoints: [],
});

const fetchDevelopPointHistory = async (monthOffset: number) => {
    const response = await axios.get(
        `/api/develop_points?monthOffset=${monthOffset}`,
    );

    developPointHistory.value = response.data as DevelopPointHistory;
};

const fetchMembers = async () => {
    const response = await axios.get("/api/members");
    members.value = response.data;
};

const monthOffset = ref(0);

const validMembers = computed(() =>
    members.value.filter((member) => member.isValid),
);

const shiftMonth = (offset: number) => {
    monthOffset.value += offset;
    fetchDevelopPointHistory(monthOffset.value);
};

const developMember = (
    notionId: string,
    developPoint: DevelopPoint,
): DevelopPointMember =>
    developPoint.members.find(
        (m) => m.notionId === notionId,
    ) as DevelopPointMember;

onMounted(() => {
    fetchDevelopPointHistory(monthOffset.value);
    fetchMembers();
});
</script>
