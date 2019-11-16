Feature: Calculate delivery hours
    In order to be the most profitable transport company
    As the shipment manager
    I need to be able to know how long it would take to deliver a set of cargos

    Scenario Outline: Compute the time to deliver a list of cargos
        When I run the time to deliver command with the list of cargos <cargos>
        Then I should read the result <result>

        Examples:
            | cargos       | result |
            | A            | 5      |
            | AB           | 5      |
            | BB           | 5      |
            | ABB          | 7      |
            | AA           | 13     |
            | AABABBAB     | 29     |
            | ABBBABAAABBB | 41     |
