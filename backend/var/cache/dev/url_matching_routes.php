<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/doc.json' => [[['_route' => 'app.swagger', '_controller' => 'nelmio_api_doc.controller.swagger'], null, ['GET' => 0], null, false, false, null]],
        '/api/doc' => [[['_route' => 'app.swagger_ui', '_controller' => 'nelmio_api_doc.controller.swagger_ui'], null, ['GET' => 0], null, false, false, null]],
        '/api/auth/login' => [[['_route' => 'app_login', '_controller' => 'App\\Controller\\AuthController::login'], null, ['POST' => 0], null, false, false, null]],
        '/api/category/create' => [[['_route' => 'create_category', '_controller' => 'App\\Controller\\CategoryController::createCategory'], null, ['POST' => 0], null, false, false, null]],
        '/api/category/all' => [[['_route' => 'get_all_categories', '_controller' => 'App\\Controller\\CategoryController::getAllCategories'], null, ['GET' => 0], null, false, false, null]],
        '/api/invite/all' => [[['_route' => 'invite_all', '_controller' => 'App\\Controller\\InvitationController::all'], null, ['GET' => 0], null, false, false, null]],
        '/api/invite/mine' => [[['_route' => 'invite_mine', '_controller' => 'App\\Controller\\InvitationController::mine'], null, ['GET' => 0], null, false, false, null]],
        '/api/member/create' => [[['_route' => 'create_member', '_controller' => 'App\\Controller\\MemberController::create'], null, ['POST' => 0], null, false, false, null]],
        '/api/member/all' => [[['_route' => 'get_all_members', '_controller' => 'App\\Controller\\MemberController::getAllMembers'], null, ['GET' => 0], null, false, false, null]],
        '/api/notification/create' => [[['_route' => 'create_notification', '_controller' => 'App\\Controller\\NotificationController::createNotification'], null, ['POST' => 0], null, false, false, null]],
        '/api/notification/all' => [[['_route' => 'get_all_notifications', '_controller' => 'App\\Controller\\NotificationController::getAllNotifications'], null, ['GET' => 0], null, false, false, null]],
        '/api/payment/create' => [[['_route' => 'create_payment', '_controller' => 'App\\Controller\\PaymentController::createPayment'], null, ['POST' => 0], null, false, false, null]],
        '/api/payment/all' => [[['_route' => 'get_all_payments', '_controller' => 'App\\Controller\\PaymentController::getAllPayments'], null, ['GET' => 0], null, false, false, null]],
        '/api/permission/assign' => [[['_route' => 'assign_permission', '_controller' => 'App\\Controller\\PermissionController::assignPermission'], null, ['POST' => 0], null, false, false, null]],
        '/api/service/create' => [[['_route' => 'create_service', '_controller' => 'App\\Controller\\ServiceController::createService'], null, ['POST' => 0], null, false, false, null]],
        '/api/service/all' => [[['_route' => 'get_all_services', '_controller' => 'App\\Controller\\ServiceController::getAllServices'], null, ['GET' => 0], null, false, false, null]],
        '/api/space/create' => [[['_route' => 'create_space', '_controller' => 'App\\Controller\\SpaceController::createSpace'], null, ['POST' => 0], null, false, false, null]],
        '/api/space/all' => [[['_route' => 'get_all_spaces', '_controller' => 'App\\Controller\\SpaceController::getAllSpaces'], null, ['GET' => 0], null, false, false, null]],
        '/api/subscription/create' => [[['_route' => 'create_subscription', '_controller' => 'App\\Controller\\SubscriptionController::createSubscription'], null, ['POST' => 0], null, false, false, null]],
        '/api/subscription/all' => [[['_route' => 'get_all_subscriptions', '_controller' => 'App\\Controller\\SubscriptionController::getAllSubscriptions'], null, ['GET' => 0], null, false, false, null]],
        '/api/subscription/mine' => [[['_route' => 'get_my_subscriptions', '_controller' => 'App\\Controller\\SubscriptionController::getMySubscriptions'], null, ['GET' => 0], null, false, false, null]],
        '/api/subscription-tag/create' => [[['_route' => 'create_subscription_tag', '_controller' => 'App\\Controller\\SubscriptionTagController::create'], null, ['POST' => 0], null, false, false, null]],
        '/api/subscription-tag/all' => [[['_route' => 'get_all_subscription_tags', '_controller' => 'App\\Controller\\SubscriptionTagController::getAll'], null, ['GET' => 0], null, false, false, null]],
        '/api/tag/create' => [[['_route' => 'create_tag', '_controller' => 'App\\Controller\\TagController::createTag'], null, ['POST' => 0], null, false, false, null]],
        '/api/tag/all' => [[['_route' => 'get_all_tags', '_controller' => 'App\\Controller\\TagController::getAllTags'], null, ['GET' => 0], null, false, false, null]],
        '/api/user/create' => [[['_route' => 'create_user', '_controller' => 'App\\Controller\\UserController::createUser'], null, ['POST' => 0], null, false, false, null]],
        '/api/user/all' => [[['_route' => 'get_all_users', '_controller' => 'App\\Controller\\UserController::getAllUsers'], null, ['GET' => 0], null, false, false, null]],
        '/api/user/me' => [[['_route' => 'api_user_me', '_controller' => 'App\\Controller\\UserController::me'], null, ['GET' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/api/(?'
                    .'|category/(?'
                        .'|([^/]++)(*:70)'
                        .'|update/([^/]++)(*:92)'
                        .'|delete/([^/]++)(*:114)'
                    .')'
                    .'|invite/(?'
                        .'|de(?'
                            .'|lete/(\\d+)(*:148)'
                            .'|cline/(\\d+)(*:167)'
                        .')'
                        .'|accept/(\\d+)(*:188)'
                    .')'
                    .'|member/(?'
                        .'|([^/]++)(*:215)'
                        .'|update/([^/]++)(*:238)'
                        .'|delete/([^/]++)(*:261)'
                        .'|invitation(?'
                            .'|s(*:283)'
                            .'|/(?'
                                .'|cancel/([^/]++)(*:310)'
                                .'|resend/([^/]++)(*:333)'
                            .')'
                        .')'
                    .')'
                    .'|notification/(?'
                        .'|([^/]++)(*:368)'
                        .'|mark\\-read/([^/]++)(*:395)'
                        .'|delete/([^/]++)(*:418)'
                    .')'
                    .'|p(?'
                        .'|ayment/(?'
                            .'|([^/]++)(*:449)'
                            .'|update/([^/]++)(*:472)'
                            .'|delete/([^/]++)(*:495)'
                        .')'
                        .'|ermission/(?'
                            .'|user/([^/]++)(*:530)'
                            .'|revoke/([^/]++)(*:553)'
                        .')'
                    .')'
                    .'|s(?'
                        .'|ervice/(?'
                            .'|([^/]++)(*:585)'
                            .'|update/([^/]++)(*:608)'
                            .'|delete/([^/]++)(*:631)'
                        .')'
                        .'|pace/(?'
                            .'|([^/]++)(*:656)'
                            .'|archive/([^/]++)(*:680)'
                            .'|delete/([^/]++)(*:703)'
                        .')'
                        .'|ubscription(?'
                            .'|/(?'
                                .'|update/([^/]++)(*:745)'
                                .'|delete/([^/]++)(*:768)'
                                .'|cancel/([^/]++)(*:791)'
                            .')'
                            .'|\\-tag/(?'
                                .'|([^/]++)(*:817)'
                                .'|delete/([^/]++)(*:840)'
                            .')'
                        .')'
                    .')'
                    .'|tag/delete/([^/]++)(*:870)'
                    .'|user/(?'
                        .'|([0-9a-fA-F-]{36})(?'
                            .'|(*:907)'
                        .')'
                        .'|delete/([0-9a-fA-F-]{36})(*:941)'
                        .'|([0-9a-fA-F-]{36})/(?'
                            .'|promote(*:978)'
                            .'|demote(*:992)'
                        .')'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        70 => [[['_route' => 'get_category_by_id', '_controller' => 'App\\Controller\\CategoryController::getCategoryById'], ['id'], ['GET' => 0], null, false, true, null]],
        92 => [[['_route' => 'update_category', '_controller' => 'App\\Controller\\CategoryController::updateCategory'], ['id'], ['PUT' => 0], null, false, true, null]],
        114 => [[['_route' => 'delete_category', '_controller' => 'App\\Controller\\CategoryController::deleteCategory'], ['id'], ['DELETE' => 0], null, false, true, null]],
        148 => [[['_route' => 'invite_delete', '_controller' => 'App\\Controller\\InvitationController::delete'], ['id'], ['DELETE' => 0], null, false, true, null]],
        167 => [[['_route' => 'invitation_decline_by_id', '_controller' => 'App\\Controller\\InvitationController::declineById'], ['id'], ['POST' => 0], null, false, true, null]],
        188 => [[['_route' => 'invitation_accept_by_id', '_controller' => 'App\\Controller\\InvitationController::acceptById'], ['id'], ['POST' => 0], null, false, true, null]],
        215 => [[['_route' => 'get_member_by_id', '_controller' => 'App\\Controller\\MemberController::getMemberById'], ['id'], ['GET' => 0], null, false, true, null]],
        238 => [[['_route' => 'update_member', '_controller' => 'App\\Controller\\MemberController::updateMember'], ['id'], ['PUT' => 0], null, false, true, null]],
        261 => [[['_route' => 'delete_member', '_controller' => 'App\\Controller\\MemberController::deleteMember'], ['id'], ['DELETE' => 0], null, false, true, null]],
        283 => [[['_route' => 'member_invitations_by_space', '_controller' => 'App\\Controller\\MemberController::invitationsBySpace'], [], ['GET' => 0], null, false, false, null]],
        310 => [[['_route' => 'member_invitation_cancel', '_controller' => 'App\\Controller\\MemberController::cancelInvitation'], ['id'], ['POST' => 0, 'DELETE' => 1], null, false, true, null]],
        333 => [[['_route' => 'member_invitation_resend', '_controller' => 'App\\Controller\\MemberController::resendInvitation'], ['id'], ['POST' => 0], null, false, true, null]],
        368 => [[['_route' => 'get_notification_by_id', '_controller' => 'App\\Controller\\NotificationController::getNotificationById'], ['id'], ['GET' => 0], null, false, true, null]],
        395 => [[['_route' => 'mark_notification_read', '_controller' => 'App\\Controller\\NotificationController::markNotificationAsRead'], ['id'], ['PATCH' => 0], null, false, true, null]],
        418 => [[['_route' => 'delete_notification', '_controller' => 'App\\Controller\\NotificationController::deleteNotification'], ['id'], ['DELETE' => 0], null, false, true, null]],
        449 => [[['_route' => 'get_payment_by_id', '_controller' => 'App\\Controller\\PaymentController::getPaymentById'], ['id'], ['GET' => 0], null, false, true, null]],
        472 => [[['_route' => 'update_payment', '_controller' => 'App\\Controller\\PaymentController::updatePayment'], ['id'], ['PUT' => 0], null, false, true, null]],
        495 => [[['_route' => 'delete_payment', '_controller' => 'App\\Controller\\PaymentController::deletePayment'], ['id'], ['DELETE' => 0], null, false, true, null]],
        530 => [[['_route' => 'get_user_permissions', '_controller' => 'App\\Controller\\PermissionController::getUserPermissions'], ['id'], ['GET' => 0], null, false, true, null]],
        553 => [[['_route' => 'revoke_permission', '_controller' => 'App\\Controller\\PermissionController::revokePermission'], ['id'], ['DELETE' => 0], null, false, true, null]],
        585 => [[['_route' => 'get_service_by_id', '_controller' => 'App\\Controller\\ServiceController::getServiceById'], ['id'], ['GET' => 0], null, false, true, null]],
        608 => [[['_route' => 'update_service', '_controller' => 'App\\Controller\\ServiceController::updateService'], ['id'], ['PUT' => 0], null, false, true, null]],
        631 => [[['_route' => 'delete_service', '_controller' => 'App\\Controller\\ServiceController::deleteService'], ['id'], ['DELETE' => 0], null, false, true, null]],
        656 => [[['_route' => 'get_space_by_id', '_controller' => 'App\\Controller\\SpaceController::getSpaceById'], ['id'], ['GET' => 0], null, false, true, null]],
        680 => [[['_route' => 'archive_space', '_controller' => 'App\\Controller\\SpaceController::archiveSpace'], ['id'], ['PATCH' => 0], null, false, true, null]],
        703 => [[['_route' => 'delete_space', '_controller' => 'App\\Controller\\SpaceController::deleteSpace'], ['id'], ['DELETE' => 0], null, false, true, null]],
        745 => [[['_route' => 'update_subscription', '_controller' => 'App\\Controller\\SubscriptionController::updateSubscription'], ['id'], ['PUT' => 0, 'PATCH' => 1], null, false, true, null]],
        768 => [[['_route' => 'delete_subscription', '_controller' => 'App\\Controller\\SubscriptionController::deleteSubscription'], ['id'], ['DELETE' => 0], null, false, true, null]],
        791 => [[['_route' => 'cancel_subscription', '_controller' => 'App\\Controller\\SubscriptionController::cancelSubscription'], ['id'], ['PATCH' => 0], null, false, true, null]],
        817 => [[['_route' => 'get_subscription_tag', '_controller' => 'App\\Controller\\SubscriptionTagController::getById'], ['id'], ['GET' => 0], null, false, true, null]],
        840 => [[['_route' => 'delete_subscription_tag', '_controller' => 'App\\Controller\\SubscriptionTagController::delete'], ['id'], ['DELETE' => 0], null, false, true, null]],
        870 => [[['_route' => 'delete_tag', '_controller' => 'App\\Controller\\TagController::deleteTag'], ['id'], ['DELETE' => 0], null, false, true, null]],
        907 => [
            [['_route' => 'get_user_by_id', '_controller' => 'App\\Controller\\UserController::getUserById'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'update_user', '_controller' => 'App\\Controller\\UserController::updateUser'], ['id'], ['PUT' => 0], null, false, true, null],
        ],
        941 => [[['_route' => 'delete_user', '_controller' => 'App\\Controller\\UserController::deleteUser'], ['id'], ['DELETE' => 0], null, false, true, null]],
        978 => [[['_route' => 'promote_user', '_controller' => 'App\\Controller\\UserController::promoteToAdmin'], ['id'], ['PATCH' => 0], null, false, false, null]],
        992 => [
            [['_route' => 'demote_user', '_controller' => 'App\\Controller\\UserController::demoteFromAdmin'], ['id'], ['PATCH' => 0], null, false, false, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
