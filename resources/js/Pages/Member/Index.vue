<template>
    <Head title="メンバー" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                メンバー
            </h2>
        </template>
        <template #default>
            <v-sheet border rounded class="max-w-7xl mx-auto mt-8">
                <v-data-table
                    :headers="headers"
                    hide-default-footer
                    density="comfortable"
                    :items-per-page="100"
                    :items="members"
                >
                    <template v-slot:top>
                        <v-toolbar flat class="bg-white">
                            <v-toolbar-title />
                            <v-btn
                                class="me-2"
                                :prepend-icon="
                                    visibleId ? 'mdi-eye-off' : 'mdi-eye'
                                "
                                rounded="lg"
                                :text="visibleId ? 'IDを非表示' : 'IDを表示'"
                                border
                                @click="toggleId"
                            />
                            <v-btn
                                class="me-2"
                                prepend-icon="mdi-plus"
                                rounded="lg"
                                text="メンバー追加"
                                border
                                @click="addMember"
                            />
                        </v-toolbar>
                    </template>
                    <template v-slot:item.title="{ value }">
                        <v-chip
                            :text="value"
                            border="thin opacity-25"
                            prepend-icon="mdi-member"
                            label
                        >
                            <template v-slot:prepend>
                                <v-icon color="medium-emphasis" />
                            </template>
                        </v-chip>
                    </template>
                    <template v-slot:item.team.name="{ value }">
                        <v-chip
                            v-if="value"
                            :text="value"
                            variant="outlined"
                            density="compact"
                            color="gray-500"
                        />
                    </template>
                    <template v-slot:item.imageUrl="{ value }">
                        <v-avatar v-if="value" size="40" :image="value" />
                    </template>
                    <template v-slot:item.isValid="{ item }">
                        <v-switch
                            :modelValue="item.isValid"
                            color="primary"
                            @change="toggleValid(item.id)"
                            hide-label
                            density="comfortable"
                            hide-details
                        />
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex ga-2 justify-end">
                            <v-icon
                                color="medium-emphasis"
                                icon="mdi-pencil"
                                @click="editMember(item.id)"
                            />

                            <v-icon
                                color="medium-emphasis"
                                icon="mdi-delete"
                                @click="removeMember(item.id)"
                            />
                        </div>
                    </template>
                </v-data-table>
            </v-sheet>
            <v-dialog v-model="dialog" max-width="500">
                <v-card :title="`メンバー${isEditing ? '編集' : '追加'}`">
                    <template v-slot:text>
                        <v-row>
                            <v-col cols="12">
                                <v-text-field
                                    v-model="record.id"
                                    label="ID"
                                    :disabled="isEditing"
                                />
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model="record.name"
                                    label="名前"
                                />
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-number-input
                                    v-model="record.targetPoint"
                                    :max="100"
                                    :min="0"
                                    :step="5"
                                    label="目標ポイント"
                                />
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-select
                                    v-model="record.team"
                                    :items="teams"
                                    item-title="name"
                                    item-value="id"
                                    label="チーム"
                                    :menu-props="{ maxHeight: '400' }"
                                    return-object
                                    single-line
                                />
                            </v-col>
                            <v-col cols="12" md="12">
                                <v-text-field
                                    v-model="record.imageUrl"
                                    label="アイコンURL"
                                />
                            </v-col>
                        </v-row>
                    </template>
                    <v-divider />
                    <v-card-actions class="bg-surface-light">
                        <v-btn
                            text="キャンセル"
                            variant="plain"
                            @click="dialog = false"
                        />
                        <v-spacer />
                        <v-btn text="保存" @click="saveMember" />
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref, shallowRef, computed } from "vue";
import axios from "axios";

import { Member, Team } from "./types";

const DEFAULT_RECORD = {
    id: "",
    name: "",
    team: {
        id: 0,
        name: "",
    },
    imageUrl: "",
    targetPoint: 25,
    isValid: true,
} as Member;

const members = ref<Member[]>([]);
const record = ref<Member>(DEFAULT_RECORD);
const dialog = shallowRef(false);
const isEditing = shallowRef(false);

const visibleId = ref(false);
const toggleId = () => (visibleId.value = !visibleId.value);

const headers = computed(() => {
    const baseHeaders = [
        { title: "アイコン", key: "imageUrl", width: "90px", sortable: false },
        { title: "名前", key: "name", sortable: true },
        { title: "チーム", key: "team.name", width: "160px", sortable: true },
        {
            title: "目標pt",
            key: "targetPoint",
            width: "120px",
            sortable: true,
        },
        {
            title: "計測対象",
            key: "isValid",
            width: "120px",
            sortable: true,
        },
        { title: "", key: "actions", width: "100px", sortable: false },
    ];
    return visibleId.value
        ? [
              { title: "ID", key: "id", width: "320px", sortable: true },
              ...baseHeaders,
          ]
        : baseHeaders;
});

onMounted(() => {
    fetchMembers();
    fetchTeams();
});

const addMember = () => {
    isEditing.value = false;
    record.value = DEFAULT_RECORD;
    dialog.value = true;
};

const editingId = ref<Member["id"] | null>(null);
const editMember = (id: Member["id"]) => {
    isEditing.value = true;

    const found = members.value.find((member) => member.id === id);

    if (!found) return;

    editingId.value = id;
    record.value = {
        id: found.id,
        name: found.name,
        team: {
            id: found.team.id,
            name: found.team.name,
        },
        imageUrl: found.imageUrl,
        targetPoint: found.targetPoint,
        isValid: found.isValid,
    };

    dialog.value = true;
};

const removeMember = async (id: Member["id"]) => {
    if (!confirm("本当に削除しますか？")) return;

    const response = await axios.delete(`/api/members/${id}`);
    fetchMembers();
    editingId.value = null;
    dialog.value = false;
};

const fetchMembers = async () => {
    const response = await axios.get("/api/members");
    members.value = response.data;
};

const saveMember = () => {
    const method = isEditing.value ? "put" : "post";
    const url = isEditing.value
        ? `/api/members/${editingId.value}`
        : "/api/members";

    axios[method](url, record.value)
        .then(() => {
            fetchMembers();
            dialog.value = false;
        })
        .catch((error) => {
            console.error("Error saving member:", error);
        });
};

const toggleValid = async (id: Member["id"]) => {
    isEditing.value = true;
    editingId.value = id;
    record.value = members.value.find((member) => member.id === id) as Member;
    record.value.isValid = !record.value.isValid;
    saveMember();
};

const teams = ref<Team[]>([]);
const fetchTeams = async () => {
    const response = await axios.get("/api/teams");
    teams.value = response.data;
};
</script>
