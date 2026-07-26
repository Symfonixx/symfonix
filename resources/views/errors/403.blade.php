@include('errors.themed', [
    'status' => 403,
    'title' => '403 Forbidden',
    'heading' => 'Access Denied',
    'message' => 'You do not have permission to access this page.',
])
