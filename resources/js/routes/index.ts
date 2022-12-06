const routes = [
    {
        path: "/crm",
        name: "crm",
        component: () => import("@/components/SDCounter/Index.vue"),
    },
    {
        path: "/error",
        name: "error",
        component: () => import("@/components/ErrorCounter/Index.vue"),
    },
];

export default routes;
