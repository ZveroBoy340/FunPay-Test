# FunPay-Test
Тестовое задание для FunPay

Current
Array
(
    [0] => SELECT name FROM users WHERE user_id = 1
    [1] => SELECT * FROM users WHERE name = 'Jack' AND block = 0
    [2] => SELECT `name`, `email` FROM users WHERE user_id = 2 AND block = 1
    [3] => UPDATE users SET `name` = 'Jack', `email` = NULL WHERE user_id = -1
    [4] => SELECT name FROM users WHERE `user_id` IN (1, 2, 3)
    [5] => SELECT name FROM users WHERE `user_id` IN (1, 2, 3) AND block = 1
)
Must to be
Array
(
    [0] => SELECT name FROM users WHERE user_id = 1
    [1] => SELECT * FROM users WHERE name = 'Jack' AND block = 0
    [2] => SELECT `name`, `email` FROM users WHERE user_id = 2 AND block = 1
    [3] => UPDATE users SET `name` = 'Jack', `email` = NULL WHERE user_id = -1
    [4] => SELECT name FROM users WHERE `user_id` IN (1, 2, 3)
    [5] => SELECT name FROM users WHERE `user_id` IN (1, 2, 3) AND block = 1
)
OK
