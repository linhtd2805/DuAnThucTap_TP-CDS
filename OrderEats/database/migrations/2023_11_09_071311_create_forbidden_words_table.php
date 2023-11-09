<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForbiddenWordsTable extends Migration
{
    public function up()
    {
        Schema::create('forbidden_words', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('forbidden_words');
    }
}


// INSERT INTO ForbiddenWords (word) VALUES ('Fraud');
// INSERT INTO ForbiddenWords (word) VALUES ('Scam');
// INSERT INTO ForbiddenWords (word) VALUES ('Illegal');
// INSERT INTO ForbiddenWords (word) VALUES ('Offensive');
// INSERT INTO ForbiddenWords (word) VALUES ('Personal attack');
// INSERT INTO ForbiddenWords (word) VALUES ('Harassment');
// INSERT INTO ForbiddenWords (word) VALUES ('Violence');
// INSERT INTO ForbiddenWords (word) VALUES ('Inappropriate content');
// INSERT INTO ForbiddenWords (word) VALUES ('Hate speech');
// INSERT INTO ForbiddenWords (word) VALUES ('Suicide, self-harm');
// INSERT INTO ForbiddenWords (word) VALUES ('Lừa đảo');
// INSERT INTO ForbiddenWords (word) VALUES ('Gian lận');
// INSERT INTO ForbiddenWords (word) VALUES ('Trái pháp luật');
// INSERT INTO ForbiddenWords (word) VALUES ('Xúc phạm');
// INSERT INTO ForbiddenWords (word) VALUES ('Tấn công cá nhân');
// INSERT INTO ForbiddenWords (word) VALUES ('Quấy rối');
// INSERT INTO ForbiddenWords (word) VALUES ('Bạo lực');
// INSERT INTO ForbiddenWords (word) VALUES ('Nội dung không phù hợp');
// INSERT INTO ForbiddenWords (word) VALUES ('Kích động, kích đến');
// INSERT INTO ForbiddenWords (word) VALUES ('Tự tử, tự hại');

