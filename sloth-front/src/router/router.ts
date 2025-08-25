import { createRouter, createWebHistory, type RouteRecordRaw } from "vue-router";

const routes: RouteRecordRaw[] = [
    {
        path: "/products",
        name: "products",
        component: () => import("@/pages/products/index.vue"),
    },
    {
        path: "/product/:id",
        name: "product.show",
        component: () => import("@/pages/products/show.vue"),
        props: true,
    },
    {
        path: "/cart",
        name: "/cart",
        component: () => import("@/components/CartDropdown.vue"),
    },
    {
        path: "/login",
        name: "login",
        component: () => import("@/pages/auth/Login.vue"),
    },
    {
        path: "/register",
        name: "register",
        component: () => import("@/pages/auth/Register.vue"),
    },
    {
        path: "/checkout",
        name: "checkout",
        component: () => import("@/pages/orders/Checkout.vue"),
    },
    {
        path: "/orders",
        name: "orders.index",
        component: () => import("@/pages/orders/Index.vue"),
    },
    {
        path: "/orders/:id",
        name: "orders.show",
        component: () => import("@/pages/orders/Show.vue"),
        props: true,
    },

];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;