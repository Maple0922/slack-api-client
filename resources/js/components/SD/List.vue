<template>
    <div class="sd-counter">
        <h2>SD質問件数</h2>
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
        <v-table height="300px">
            <thead>
                <tr>
                    <th class="text-left">Name</th>
                    <th class="text-left">Calories</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in desserts" :key="item.name">
                    <td>{{ item.name }}</td>
                    <td>{{ item.calories }}</td>
                </tr>
            </tbody>
        </v-table>
    </div>
</template>

<script setup lang="ts">
import axios from "axios";
import { ref, Ref, onMounted } from "vue";

interface SD {
    count: number;
    week: string;
}

const desserts = [
    {
        name: "Frozen Yogurt",
        calories: 159,
    },
    {
        name: "Ice cream sandwich",
        calories: 237,
    },
    {
        name: "Eclair",
        calories: 262,
    },
    {
        name: "Cupcake",
        calories: 305,
    },
    {
        name: "Gingerbread",
        calories: 356,
    },
    {
        name: "Jelly bean",
        calories: 375,
    },
    {
        name: "Lollipop",
        calories: 392,
    },
    {
        name: "Honeycomb",
        calories: 408,
    },
    {
        name: "Donut",
        calories: 452,
    },
    {
        name: "KitKat",
        calories: 518,
    },
];

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
    // &__table {
    //     width: 100%;
    //     border-collapse: collapse;
    //     th {
    //         padding: 10px;
    //         text-align: center;
    //     }
    //     td {
    //         padding: 10px;
    //         text-align: center;
    //     }
    // }
}
</style>
