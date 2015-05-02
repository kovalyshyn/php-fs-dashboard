CREATE OR REPLACE FUNCTION public.fn_cdrupdatebillings()
 RETURNS TRIGGER
 LANGUAGE plpgsql
AS $function$
DECLARE
    blacklist boolean := TRUE;
    num_a boolean := FALSE;
    num_b boolean := FALSE;
BEGIN 

IF NEW.destination_id IS NOT NULL THEN

    PERFORM "id" FROM "Destinations" WHERE id = NEW.destination_id AND ("numA" = true OR "numB" = true);
    IF NOT FOUND THEN
        blacklist := FALSE;
    ELSE
        num_a := (SELECT "numA" FROM "Destinations" WHERE id = NEW.destination_id );
        num_b := (SELECT "numB" FROM "Destinations" WHERE id = NEW.destination_id );
    END IF;

-- Process Before Answer
    IF blacklist IS TRUE AND EXISTS (SELECT "id" FROM "Destinations" as "D" WHERE id = NEW.destination_id 
        AND ("progress_before_answer" > 0 AND NEW.billsec > 0 AND "progress_before_answer" > NEW.progress_mediasec) 
        AND ( 
            ( (num_a IS TRUE)  AND ( (SELECT COUNT("uuid") FROM cdr AS "C" WHERE 
                "C"."start_stamp"::TIMESTAMP > (now() - "D"."repeat_calls_minutes" * INTERVAL '1 minute') AND "C"."caller_id_number" like NEW.caller_id_number)  >= "D"."repeat_calls" ) 
            ) 
        OR ( (num_b IS TRUE)  AND ( (SELECT COUNT("uuid") FROM cdr AS "C" WHERE 
                "C"."start_stamp"::TIMESTAMP > (now() - "D"."repeat_calls_minutes" * INTERVAL '1 minute') AND "C"."destination_number" like NEW.destination_number)  >= "D"."repeat_calls" ) ) 
            )
        )
    THEN
        IF num_a IS TRUE THEN
           INSERT INTO "NumberList" ( "caller_id_number", "added", "destinations","whitelist", "description") VALUES ( NEW.caller_id_number, NOW(), NEW.destination_id, false, 'ProgressMedia before answer: ' || NEW.progress_mediasec || ' and Billsec: ' || NEW.billsec );
        END IF;
        IF num_b IS TRUE THEN
           INSERT INTO "NumberList" ( "callee_id_number", "added", "destinations","whitelist", "description") VALUES ( NEW.destination_number, NOW(), NEW.destination_id, false, 'ProgressMedia before answer: ' || NEW.progress_mediasec || ' and Billsec: ' || NEW.billsec );
        END IF;
        blacklist := FALSE;
    END IF;
        
-- Process Without Answer
    IF blacklist IS TRUE AND EXISTS (SELECT "id" FROM "Destinations" AS "D" WHERE id = NEW.destination_id 
        AND ("progress_without_answer" > 0 AND NEW.billsec = 0 AND NEW.progress_mediasec > 0 AND "progress_without_answer" > NEW.progress_mediasec ) 
        AND ( 
            ( (num_a IS TRUE)  AND ( (SELECT COUNT("uuid") FROM cdr AS "C" WHERE 
                "C"."start_stamp"::TIMESTAMP > (now() - "D"."repeat_calls_minutes_without_answer" * INTERVAL '1 minute') AND "C"."caller_id_number" LIKE NEW.caller_id_number)  >= "D"."repeat_calls_without_answer" ) 
            ) 
        OR ( (num_b IS TRUE)  AND ( (SELECT COUNT("uuid") FROM cdr AS "C" WHERE 
                "C"."start_stamp"::TIMESTAMP > (now() - "D"."repeat_calls_minutes_without_answer" * INTERVAL '1 minute') AND "C"."destination_number" LIKE NEW.destination_number)  >= "D"."repeat_calls_without_answer" ) ) 
            )
        )
    THEN
        IF num_a IS TRUE THEN
           INSERT INTO "NumberList" ( "caller_id_number", "added", "destinations","whitelist", "description") VALUES ( NEW.caller_id_number, NOW(), NEW.destination_id, FALSE, 'ProgressMedia without answer with media: ' || NEW.progress_mediasec );
        END IF;
        IF num_b IS TRUE THEN
           INSERT INTO "NumberList" ( "callee_id_number", "added", "destinations","whitelist", "description") VALUES ( NEW.destination_number, NOW(), NEW.destination_id, FALSE, 'ProgressMedia without answer with media: ' || NEW.progress_mediasec );
        END IF;
        blacklist := FALSE;
    END IF;

-- Process No Answer
    IF blacklist IS TRUE AND EXISTS (SELECT "id" FROM "Destinations" AS "D" WHERE id = NEW.destination_id 
        AND ("progress_no_answer" IS TRUE AND NEW.billsec = 0 AND NEW.progress_mediasec > 0) 
        AND ( 
            ( (num_a IS TRUE)  AND ( (SELECT COUNT("uuid") FROM cdr AS "C" WHERE 
                "C"."start_stamp"::TIMESTAMP > (now() - "D"."repeat_calls_minutes_no_answer" * INTERVAL '1 minute') AND "C"."caller_id_number" LIKE NEW.caller_id_number)  >= "D"."repeat_calls_no_answer" ) 
            ) 
        OR ( (num_b IS TRUE)  AND ( (SELECT COUNT("uuid") FROM cdr AS "C" WHERE 
                "C"."start_stamp"::TIMESTAMP > (now() - "D"."repeat_calls_minutes_no_answer" * INTERVAL '1 minute') AND "C"."destination_number" LIKE NEW.destination_number)  >= "D"."repeat_calls_no_answer" ) ) 
            )
        )
    THEN
        IF num_a IS TRUE THEN
           INSERT INTO "NumberList" ( "caller_id_number", "added", "destinations","whitelist", "description") VALUES ( NEW.caller_id_number, NOW(), NEW.destination_id, FALSE, 'ProgressMedia without answer: ' || NEW.progress_mediasec );
        END IF;
        IF num_b IS TRUE THEN
           INSERT INTO "NumberList" ( "callee_id_number", "added", "destinations","whitelist", "description") VALUES ( NEW.destination_number, NOW(), NEW.destination_id, FALSE, 'ProgressMedia without answer: ' || NEW.progress_mediasec );
        END IF;
        blacklist := FALSE;
    END IF;

END IF;

-- UPDATE VALUES
IF NEW.billsec > 0 THEN
    IF NEW.context = 'public' THEN
        UPDATE "Getaways" SET "connected" = now(), "selected" = now(), "last_hangup_cause" = NEW.hangup_cause WHERE "Getaways"."id" = NEW.gw_id;
    ELSE
        IF (select delay_all from "Getaways" where id = NEW.gw_id) > 0 THEN
            UPDATE "Getaways" SET "connected" = now(), "selected" = now(), "last_hangup_cause" = NEW.hangup_cause WHERE "Getaways"."id" = NEW.gw_id;
	ELSE
	    UPDATE "Getaways" SET "selected" = now(), "last_hangup_cause" = NEW.hangup_cause WHERE "Getaways"."id" = NEW.gw_id;
	END IF;
    END IF;
END IF;

RETURN new;
END;
$function$;
COMMENT ON FUNCTION "public"."fn_cdrupdatebillings"() IS NULL;
