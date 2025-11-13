import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        VitePWA({
            registerType: 'autoUpdate',
            manifest: {
                name: 'Recettes Zéro Gaspi',
                short_name: 'Zéro Gaspi',
                description: 'Application communautaire de recettes anti-gaspillage avec planification des repas et gestion du garde-manger',
                theme_color: '#059669',
                background_color: '#ffffff',
                display: 'standalone',
                start_url: '/',
                icons: [
                    {
                        src: '/images/icon-192x192.png',
                        sizes: '192x192',
                        type: 'image/png',
                        purpose: 'any maskable'
                    },
                    {
                        src: '/images/icon-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any maskable'
                    }
                ]
            },
            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg,woff,woff2}'],
                runtimeCaching: [
                    {
                        urlPattern: /^https:\/\/.*\.(png|jpg|jpeg|svg|gif|webp)$/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'images-cache',
                            expiration: {
                                maxEntries: 100,
                                maxAgeSeconds: 60 * 60 * 24 * 30
                            }
                        }
                    },
                    {
                        urlPattern: /^\/api\/.*/i,
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'api-cache',
                            networkTimeoutSeconds: 10,
                            expiration: {
                                maxEntries: 50,
                                maxAgeSeconds: 60 * 60
                            }
                        }
                    },
                    {
                        urlPattern: /^\/recipes\/.*/i,
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'recipes-cache',
                            networkTimeoutSeconds: 5,
                            expiration: {
                                maxEntries: 100,
                                maxAgeSeconds: 60 * 60 * 24
                            }
                        }
                    },
                    {
                        urlPattern: /^\/favorites.*/i,
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'favorites-cache',
                            networkTimeoutSeconds: 5,
                            expiration: {
                                maxEntries: 50,
                                maxAgeSeconds: 60 * 60 * 24
                            }
                        }
                    },
                    {
                        urlPattern: /^\/meal-plans.*/i,
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'meal-plans-cache',
                            networkTimeoutSeconds: 5,
                            expiration: {
                                maxEntries: 20,
                                maxAgeSeconds: 60 * 60 * 24
                            }
                        }
                    }
                ]
            },
            devOptions: {
                enabled: false
            }
        })
    ],
});
