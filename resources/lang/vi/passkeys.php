<?php

declare(strict_types=1);

return [
    'provider' => [
        'label' => 'Dùng passkey',
    ],

    'management' => [
        'heading' => 'Passkey',
        'description' => 'Passkey cho phép bạn đăng nhập bằng vân tay, khuôn mặt hoặc mã PIN của thiết bị.',
        'enabled' => 'Đã bật',
        'disabled' => 'Đã tắt',
        'empty' => 'Chưa có passkey nào được đăng ký.',
        'add' => 'Thêm passkey',
        'waiting' => 'Đang chờ thiết bị…',
        'default_name' => 'Passkey',
        'rename' => 'Đổi tên',
        'renamed' => 'Đã đổi tên passkey.',
        'delete' => 'Xóa',
        'delete_confirmation' => 'Bạn có chắc chắn muốn xóa passkey ":name"?',
        'never_used' => 'Chưa sử dụng',
        'columns' => [
            'name' => 'Tên',
            'last_used' => 'Sử dụng lần cuối',
            'actions' => 'Thao tác',
        ],
        'registered' => 'Đăng ký passkey thành công.',
        'deleted' => 'Đã xóa passkey.',
    ],

    'challenge' => [
        'unsupported' => 'Trình duyệt của bạn không hỗ trợ passkey.',
        'failed' => 'Không thể xác minh passkey đó. Vui lòng thử lại.',
        'callout' => [
            'heading' => 'Đăng nhập bằng passkey',
            'description' => 'Tiếp tục chọn một passkey từ thiết bị hoặc trình quản lý mật khẩu của bạn.',
            'waiting' => 'Đang chờ passkey của bạn…',
        ],
    ],

    'register' => [
        'name_label' => 'Tên passkey',
    ],

    'login' => [
        'button' => 'Đăng nhập bằng passkey',
        'failed' => 'Không thể xác minh passkey của bạn. Vui lòng thử lại.',
    ],
];
