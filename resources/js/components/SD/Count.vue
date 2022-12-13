<template>
    <v-table>
        <thead>
            <tr>
                <th>週</th>
                <th>件数</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(sd, key) in sds" :key="key">
                <td>{{ sd.week }}</td>
                <td>{{ sd.count }}</td>
            </tr>
        </tbody>
    </v-table>
</template>

<script setup lang="ts">
import axios from "axios";
import { ref, Ref, onMounted } from "vue";

interface SD {
    count: number;
    week: string;
}

const sds: Ref<SD[]> = ref([]);

const fetchSDCount = async () => {
    const { data } = await axios.get("/api/sds/count");
    sds.value = data;
};

onMounted(async () => {
    fetchSDCount();
});
</script>
