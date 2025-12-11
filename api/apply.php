<?php
/**
 * 보험 가입 신청 API
 * POST 요청으로 보험 가입 정보를 받아 데이터베이스에 저장합니다.
 */

require_once '../config.php';

setJsonHeader();

// POST 요청만 허용
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'POST 메서드만 허용됩니다.'
    ]);
    exit;
}

// 입력 데이터 받기
$input = json_decode(file_get_contents('php://input'), true);

// JSON 파싱 실패 시 폼 데이터로 시도
if (!$input) {
    $input = $_POST;
}

// 필수 필드 검증
$required_fields = [
    'groupName', 'contactName', 'contactPhone', 'email',
    'travelers', 'destination', 'departureDate', 'returnDate', 'insuranceType'
];

$errors = [];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        $errors[] = $field . ' 필드는 필수입니다.';
    }
}

// 추가 유효성 검사
if (!empty($input['email']) && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = '올바른 이메일 형식이 아닙니다.';
}

if (!empty($input['travelers']) && intval($input['travelers']) < 2) {
    $errors[] = '여행자 수는 최소 2명 이상이어야 합니다.';
}

if (!empty($input['departureDate']) && !empty($input['returnDate'])) {
    $departure = new DateTime($input['departureDate']);
    $return = new DateTime($input['returnDate']);
    $today = new DateTime();
    
    if ($departure < $today) {
        $errors[] = '출발일은 오늘 이후 날짜여야 합니다.';
    }
    
    if ($return <= $departure) {
        $errors[] = '귀국일은 출발일보다 늦어야 합니다.';
    }
}

// 유효성 검사 실패 시 에러 반환
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => '입력 데이터 검증 실패',
        'errors' => $errors
    ]);
    exit;
}

// 데이터베이스 연결
$conn = getDBConnection();
if (!$conn) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '데이터베이스 연결에 실패했습니다.'
    ]);
    exit;
}

// SQL 인젝션 방지를 위한 prepared statement 사용
$stmt = $conn->prepare("
    INSERT INTO insurance_applications 
    (group_name, contact_name, contact_phone, email, travelers_count, 
     destination, departure_date, return_date, insurance_type, additional_info)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '데이터베이스 쿼리 준비에 실패했습니다: ' . $conn->error
    ]);
    $conn->close();
    exit;
}

// 데이터 바인딩
$groupName = $input['groupName'];
$contactName = $input['contactName'];
$contactPhone = $input['contactPhone'];
$email = $input['email'];
$travelersCount = intval($input['travelers']);
$destination = $input['destination'];
$departureDate = $input['departureDate'];
$returnDate = $input['returnDate'];
$insuranceType = $input['insuranceType'];
$additionalInfo = !empty($input['additionalInfo']) ? $input['additionalInfo'] : null;

$stmt->bind_param(
    "ssssisssss",
    $groupName,
    $contactName,
    $contactPhone,
    $email,
    $travelersCount,
    $destination,
    $departureDate,
    $returnDate,
    $insuranceType,
    $additionalInfo
);

// 쿼리 실행
if ($stmt->execute()) {
    $applicationId = $conn->insert_id;
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => '보험 가입 신청이 완료되었습니다.',
        'data' => [
            'applicationId' => $applicationId,
            'groupName' => $groupName,
            'contactName' => $contactName,
            'email' => $email
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '데이터 저장에 실패했습니다: ' . $stmt->error
    ]);
}

// 리소스 정리
$stmt->close();
$conn->close();

