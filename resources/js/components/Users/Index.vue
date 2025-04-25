<template>
    <v-sheet border rounded>
        <v-data-table
            :headers="headers"
            :hide-default-footer="members.length < 11"
            :items="members"
        >
            <template v-slot:top>
                <v-toolbar flat>
                    <v-toolbar-title>
                        <v-icon
                            color="medium-emphasis"
                            icon="mdi-account"
                            size="x-small"
                            start
                        />
                        メンバー
                    </v-toolbar-title>

                    <v-btn
                        class="me-2"
                        :prepend-icon="visibleId ? 'mdi-eye-off' : 'mdi-eye'"
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
                        @click="add"
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

            <template v-slot:item.actions="{ item }">
                <div class="d-flex ga-2 justify-end">
                    <v-icon
                        color="medium-emphasis"
                        icon="mdi-pencil"
                        size="small"
                        @click="edit(item.id)"
                    />

                    <v-icon
                        color="medium-emphasis"
                        icon="mdi-delete"
                        size="small"
                        @click="remove(item.id)"
                    />
                </div>
            </template>

            <template v-slot:no-data>
                <v-btn
                    prepend-icon="mdi-backup-restore"
                    rounded="lg"
                    text="Reset data"
                    variant="text"
                    border
                    @click="reset"
                />
            </template>
        </v-data-table>
    </v-sheet>

    <v-dialog v-model="dialog" max-width="500">
        <v-card
            :subtitle="`${isEditing ? 'Update' : 'Create'} your favorite member`"
            :title="`${isEditing ? 'Edit' : 'Add'} a member`"
        >
            <template v-slot:text>
                <v-row>
                    <v-col cols="12">
                        <v-text-field v-model="record.title" label="Title" />
                    </v-col>

                    <v-col cols="12" md="6">
                        <v-text-field v-model="record.author" label="Author" />
                    </v-col>

                    <v-col cols="12" md="6">
                        <v-select
                            v-model="record.genre"
                            :items="[
                                'Fiction',
                                'Dystopian',
                                'Non-Fiction',
                                'Sci-Fi',
                            ]"
                            label="Genre"
                        />
                    </v-col>

                    <v-col cols="12" md="6">
                        <v-number-input
                            v-model="record.year"
                            :max="adapter.getYear(adapter.date())"
                            :min="1"
                            label="Year"
                        />
                    </v-col>

                    <v-col cols="12" md="6">
                        <v-number-input
                            v-model="record.pages"
                            :min="1"
                            label="Pages"
                        />
                    </v-col>
                </v-row>
            </template>

            <v-divider />

            <v-card-actions class="bg-surface-light">
                <v-btn text="Cancel" variant="plain" @click="dialog = false" />

                <v-spacer />

                <v-btn text="Save" @click="save" />
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script setup>
import { onMounted, ref, shallowRef, computed } from "vue";
import { useDate } from "vuetify";

const adapter = useDate();

const DEFAULT_RECORD = {
    id: "",
    name: "",
    imageUrl: "",
    targetPoint: 0,
    isValid: true,
};

const members = ref([]);
const record = ref(DEFAULT_RECORD);
const dialog = shallowRef(false);
const isEditing = shallowRef(false);

const headers = computed(() => [
    {
        text: "ID",
        value: "id",
        width: visibleId.value ? 100 : 0,
        sortable: true,
    },
    {
        text: "名前",
        value: "name",
        sortable: true,
    },
    {
        text: "アイコン",
        value: "imageUrl",
        width: 100,
        sortable: false,
    },
    {
        text: "目標ポイント",
        value: "targetPoint",
        sortable: true,
    },
    {
        text: "有効",
        value: "isValid",
        sortable: true,
    },
]);

const visibleId = ref(false);
const toggleId = () => (visibleId.value = !visibleId.value);

onMounted(() => {
    fetchMembers();
});

const fetchMembers = async () => {
    const response = await axios.get("/api/members");
    members.value = response.data;
};

function add() {
    isEditing.value = false;
    record.value = DEFAULT_RECORD;
    dialog.value = true;
}

function edit(id) {
    isEditing.value = true;

    const found = members.value.find((member) => member.id === id);

    record.value = {
        id: found.id,
        name: found.name,
        imageUrl: found.imageUrl,
        targetPoint: found.targetPoint,
        isValid: found.isValid,
    };

    dialog.value = true;
}

function remove(id) {
    const index = members.value.findIndex((member) => member.id === id);
    members.value.splice(index, 1);
}

function save() {
    if (isEditing.value) {
        const index = members.value.findIndex(
            (member) => member.id === record.value.id,
        );
        members.value[index] = record.value;
    } else {
        record.value.id = members.value.length + 1;
        members.value.push(record.value);
    }

    dialog.value = false;
}
</script>
