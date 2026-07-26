@include('errors.themed', [
    'status' => 401,
    'title' => '401 Unauthorized',
    'heading' => 'Authentication Required',
    'message' => 'You need to sign in to access this page.',
    'showLogin' => true,
])
