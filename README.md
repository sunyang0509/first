# 단체여행자보험 가입 사이트

단체여행자보험 가입을 위한 웹사이트입니다. PHP와 MySQL을 사용하여 백엔드가 구현되어 있습니다.

## 기술 스택

- **프론트엔드**: HTML5, CSS3, JavaScript (Vanilla JS)
- **백엔드**: PHP 7.4+
- **데이터베이스**: MySQL 5.7+ / MariaDB 10.3+
- **호스팅**: 카페24 호스팅 환경

## 카페24 호스팅 설치 가이드

### 1. 파일 업로드

1. 카페24 호스팅 관리자 페이지에 로그인
2. FTP 또는 파일 관리자를 통해 다음 파일들을 업로드:
   - `index.html` → 웹 루트 디렉토리 (`public_html` 또는 `www`)
   - `api/` 폴더 → 웹 루트 디렉토리
   - `config.php` → 웹 루트 디렉토리

### 2. 데이터베이스 설정

1. 카페24 관리자 페이지에서 MySQL 데이터베이스 생성
   - 데이터베이스 이름, 사용자명, 비밀번호 확인
   - 호스트 주소 확인 (보통 `localhost` 또는 카페24 제공 호스트)

2. phpMyAdmin 또는 MySQL 클라이언트로 접속하여 `database.sql` 파일 실행
   ```sql
   -- database.sql 파일의 내용을 실행
   ```

3. `config.php` 파일 수정
   ```php
   define('DB_HOST', 'localhost');  // 카페24에서 제공하는 DB 호스트
   define('DB_USER', 'your_db_username');  // 생성한 DB 사용자명
   define('DB_PASS', 'your_db_password');  // 생성한 DB 비밀번호
   define('DB_NAME', 'your_db_name');  // 생성한 DB 이름
   ```

### 3. 파일 권한 설정

- `api/` 폴더: 읽기/실행 권한 (755)
- `config.php`: 읽기 권한 (644)
- `index.html`: 읽기 권한 (644)

### 4. 테스트

1. 웹 브라우저에서 사이트 접속
2. 보험 가입 폼 작성 후 제출
3. 데이터베이스에 데이터가 저장되었는지 확인

## 파일 구조

```
first/
├── index.html          # 메인 HTML 파일
├── config.php          # 데이터베이스 연결 설정
├── database.sql        # MySQL 테이블 생성 스크립트
├── api/
│   └── apply.php       # 보험 가입 신청 API
├── doc/
│   └── 작업내용.md     # 프로젝트 문서
└── README.md           # 이 파일
```

## API 엔드포인트

### POST /api/apply.php

보험 가입 신청을 처리합니다.

**요청 본문 (JSON):**
```json
{
  "groupName": "ABC 여행사",
  "contactName": "홍길동",
  "contactPhone": "010-1234-5678",
  "email": "example@email.com",
  "travelers": 10,
  "destination": "일본",
  "departureDate": "2025-12-25",
  "returnDate": "2025-12-30",
  "insuranceType": "standard",
  "additionalInfo": "추가 요청사항"
}
```

**응답 (성공):**
```json
{
  "success": true,
  "message": "보험 가입 신청이 완료되었습니다.",
  "data": {
    "applicationId": 1,
    "groupName": "ABC 여행사",
    "contactName": "홍길동",
    "email": "example@email.com"
  }
}
```

**응답 (실패):**
```json
{
  "success": false,
  "message": "입력 데이터 검증 실패",
  "errors": ["이메일 필드는 필수입니다."]
}
```

## 데이터베이스 스키마

### insurance_applications 테이블

| 컬럼명 | 타입 | 설명 |
|--------|------|------|
| id | INT(11) | 자동 증가 기본키 |
| group_name | VARCHAR(255) | 단체명/회사명 |
| contact_name | VARCHAR(100) | 담당자 성명 |
| contact_phone | VARCHAR(20) | 연락처 |
| email | VARCHAR(255) | 이메일 |
| travelers_count | INT(11) | 여행자 수 |
| destination | VARCHAR(255) | 여행지 |
| departure_date | DATE | 출발일 |
| return_date | DATE | 귀국일 |
| insurance_type | VARCHAR(50) | 보험 종류 |
| additional_info | TEXT | 추가 요청사항 |
| status | VARCHAR(20) | 상태 (pending/confirmed/cancelled) |
| created_at | DATETIME | 신청일시 |
| updated_at | DATETIME | 수정일시 |

## 주요 기능

- ✅ 보험 정보 안내
- ✅ 가입 신청 폼
- ✅ 클라이언트 사이드 유효성 검사
- ✅ 서버 사이드 유효성 검사
- ✅ 데이터베이스 저장
- ✅ 에러 처리 및 사용자 피드백
- ✅ 반응형 웹 디자인

## 보안 고려사항

1. **SQL 인젝션 방지**: Prepared Statement 사용
2. **입력 데이터 검증**: 클라이언트 및 서버 양쪽에서 검증
3. **에러 메시지**: 상세한 에러 정보 노출 방지
4. **CORS 설정**: 필요시 `config.php`에서 조정

## 향후 개선 사항

- [ ] 관리자 페이지 (신청 내역 조회)
- [ ] 이메일 알림 기능
- [ ] 보험료 계산기
- [ ] 파일 업로드 (여행자 명단)
- [ ] 약관 동의 체크박스
- [ ] CSRF 토큰 추가
- [ ] HTTPS 적용

## 브라우저 호환성

- Chrome (최신 버전)
- Firefox (최신 버전)
- Safari (최신 버전)
- Edge (최신 버전)
- 모바일 브라우저 (iOS Safari, Chrome Mobile)

## 문의사항

카페24 호스팅 관련 문의는 카페24 고객센터로 연락하시기 바랍니다.

