const routes = [
    {
        path: "/sd",
        name: "sdCount",
        children: [
            {
                path: "count",
                name: "sdCount",
                component: () => import("@/components/SD/Count.vue"),
            },
            {
                path: "list",
                name: "sdList",
                component: () => import("@/components/SD/List.vue"),
            },
        ],
    },
    {
        path: "/error",
        name: "error",
        children: [
            {
                path: "count",
                name: "errorCount",
                component: () => import("@/components/Error/Count.vue"),
            },
        ],
    },
];

export default routes;
