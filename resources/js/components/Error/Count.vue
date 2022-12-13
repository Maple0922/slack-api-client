<template>
    <div class="error-counter">
        <table class="error-counter__table" border="1">
            <thead>
                <tr>
                    <th></th>
                    <th>crm-admin</th>
                    <th>crm-expert</th>
                    <th>crm-bot</th>
                    <th>money-career</th>
                    <th>合計</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(error, key) in errors" :key="key">
                    <td>{{ error.week }}</td>
                    <td>{{ error.errors["0"].count }}</td>
                    <td>{{ error.errors["1"].count }}</td>
                    <td>{{ error.errors["2"].count }}</td>
                    <td>{{ error.errors["3"].count }}</td>
                    <td>
                        {{
                            error.errors
                                .map((e) => e.count)
                                .reduce((a, b) => a + b)
                        }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import axios from "axios";
import { ref, Ref, onMounted } from "vue";

interface Error {
    errors: {
        channel: string;
        count: number;
    }[];
    week: string;
}

const errors: Ref<Error[]> = ref([]);

const fetchErrors = async () => {
    const { data } = await axios.get("/api/errors/count");
    errors.value = data;
};

onMounted(async () => {
    fetchErrors();
});
</script>

<style lang="scss" scoped>
.error-counter {
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
