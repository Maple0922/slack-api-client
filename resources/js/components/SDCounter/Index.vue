<template>
    <div class="sd-counter">
        <h2>SD質問件数</h2>
        <table class="sd-counter__table" border="1">
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
        </table>
    </div>
</template>

<script setup lang="ts">
import axios from "axios";
import { ref, Ref, onMounted } from "vue";

interface SD {
    count: number;
    week: string;
}

const sds: Ref<SD[]> = ref([]);

const fetchSDs = async () => {
    const { data } = await axios.get("/api/count/sds");
    sds.value = data;
};

onMounted(async () => {
    fetchSDs();
});
</script>

<style lang="scss" scoped>
.sd-counter {
    &__table {
        width: 100%;
        border-collapse: collapse;
        th {
            padding: 10px;
            text-align: center;
        }
        td {
            padding: 10px;
            text-align: center;
        }
    }
}
</style>
