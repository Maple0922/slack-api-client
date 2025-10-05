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
                <v-table
                    density="compact"
                    fixedHeader
                    fixedFooter
                    height="78vh"
                >
                    <thead>
                        <tr>
                            <th class="text-left">
                                <v-row class="items-center">
                                    <v-col cols="auto" class="px-1">
                                        <v-btn
                                            density="compact"
                                            variant="text"
                                            icon="mdi-chevron-left"
                                            @click="() => shiftMonth(-6)"
                                        />
                                    </v-col>
                                    <v-col cols="auto" class="px-1">
                                        <v-tooltip location="top">
                                            <template #activator="{ props }">
                                                <span
                                                    class="text-lg"
                                                    v-bind="props"
                                                    >{{ dateRange.start }}
                                                    ~
                                                    {{ dateRange.end }}</span
                                                >
                                            </template>
                                            <template #default>
                                                <span
                                                    >Last updated:
                                                    {{ updatedAt ?? "-" }}</span
                                                >
                                            </template>
                                        </v-tooltip>
                                    </v-col>
                                    <v-col cols="auto" class="px-1">
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
                            <th class="text-center">
                                <span class="text-lg font-bold">Total</span>
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
                                    <p class="text-lg">
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
                            <td class="text-center font-bold">
                                <p class="text-lg">
                                    {{
                                        Math.round(
                                            (developInReviewDate(
                                                developPoint.inReviewDate,
                                            ).totalPoint /
                                                developInReviewDate(
                                                    developPoint.inReviewDate,
                                                ).totalTarget) *
                                                1000,
                                        ) / 10
                                    }}%
                                </p>
                                <p class="text-xs">
                                    ({{
                                        developInReviewDate(
                                            developPoint.inReviewDate,
                                        ).totalPoint
                                    }}/{{
                                        developInReviewDate(
                                            developPoint.inReviewDate,
                                        ).totalTarget
                                    }})
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-lg font-bold">Total</td>
                            <td
                                v-for="member in validMembers"
                                :key="member.notionId"
                                class="text-center font-bold"
                            >
                                <template
                                    v-if="
                                        developMemberTotalPoint(member.notionId)
                                    "
                                >
                                    <p class="text-lg">
                                        {{
                                            Math.round(
                                                (developMemberTotalPoint(
                                                    member.notionId,
                                                ).totalPoint /
                                                    developMemberTotalPoint(
                                                        member.notionId,
                                                    ).totalTarget) *
                                                    1000,
                                            ) / 10
                                        }}%
                                    </p>
                                    <p class="text-xs">
                                        ({{
                                            developMemberTotalPoint(
                                                member.notionId,
                                            ).totalPoint
                                        }}/{{
                                            developMemberTotalPoint(
                                                member.notionId,
                                            ).totalTarget
                                        }})
                                    </p>
                                </template>
                                <template v-else> - </template>
                            </td>
                            <td class="text-center font-black">
                                <p class="text-2xl">
                                    {{
                                        Math.round(
                                            (totalPoint.totalPoint /
                                                totalPoint.totalTarget) *
                                                1000,
                                        ) / 10 || 0
                                    }}%
                                </p>
                                <p class="text-xs">
                                    ({{ totalPoint.totalPoint }}/{{
                                        totalPoint.totalTarget
                                    }})
                                </p>
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
import { onMounted, ref, computed, toRefs, reactive } from "vue";
import axios from "axios";

import {
    DevelopPoint,
    DevelopPointMember,
    DevelopPointHistory,
    MemberTotalPoint,
    InReviewDateTotalPoint,
} from "./types";
import { Member } from "../Member/types";

const members = ref<Member[]>([]);

const developPointHistory = reactive<DevelopPointHistory>({
    dateRange: {
        start: "",
        end: "",
    },
    points: [],
    memberTotalPoints: [],
    inReviewDateTotalPoints: [],
    totalPoint: {
        totalPoint: 0,
        totalTarget: 0,
    },
    updatedAt: null,
});

const fetchDevelopPointHistory = async (monthOffset: number) => {
    const response = await axios.get(
        `/api/develop_points?monthOffset=${monthOffset}`,
    );

    Object.assign(developPointHistory, response.data);
};

const {
    dateRange,
    memberTotalPoints,
    inReviewDateTotalPoints,
    totalPoint,
    updatedAt,
} = toRefs(developPointHistory);

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

const developInReviewDate = (inReviewDate: string): InReviewDateTotalPoint =>
    inReviewDateTotalPoints.value.find(
        (totalPoint) => totalPoint.inReviewDate === inReviewDate,
    ) as InReviewDateTotalPoint;

const developMemberTotalPoint = (notionId: string): MemberTotalPoint =>
    memberTotalPoints.value.find(
        (totalPoint) => totalPoint.notionId === notionId,
    ) as MemberTotalPoint;

onMounted(() => {
    fetchDevelopPointHistory(monthOffset.value);
    fetchMembers();
});
</script>
