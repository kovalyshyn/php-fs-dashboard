-- Blacklist: repeat_calls_no_answer
ALTER TABLE "public"."Destinations" ADD "repeat_calls_no_answer" INTEGER;

-- Blacklist: repeat_calls_minutes_no_answer
ALTER TABLE "public"."Destinations" ADD "repeat_calls_minutes_no_answer" INTEGER;

-- Blacklist: repeat_calls_without_answer
ALTER TABLE "public"."Destinations" ADD "repeat_calls_without_answer" INTEGER;

-- Blacklist: repeat_calls_minutes_without_answer
ALTER TABLE "public"."Destinations" ADD "repeat_calls_minutes_without_answer" INTEGER;

