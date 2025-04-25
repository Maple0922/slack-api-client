const routes = [
    {
        path: "/users",
        name: "users",
        component: () => import("@/components/Users/Index.vue"),
    },
];

export default routes;
