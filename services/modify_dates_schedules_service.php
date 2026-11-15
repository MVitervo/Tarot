
<?php

class ModifyDatesSchedulesService
{
    private PDO $conn;
    private string $username;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->username = $_SESSION['admin_username'] ?? '';
    }

    public function getDates()
    {
        try {
            $queryGetDates = "SELECT * FROM calendar_exceptions";

            $stmtGetDates = $this->conn->prepare($queryGetDates);
            $stmtGetDates->execute();

            $resultGetDates = $stmtGetDates->fetchAll(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'data' => $resultGetDates
            ];

        } catch (PDOException $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function getspecificDate(ModifyDatesSchedules $specificDate)
    {
        try {
            $queryGetSchedules = "SELECT *
                                  FROM calendar_exceptions
                                  WHERE `Date` = :Date";

            $stmtGetSchedules = $this->conn->prepare($queryGetSchedules);
            $stmtGetSchedules->bindParam(':Date', $specificDate->date, PDO::PARAM_STR);
            $stmtGetSchedules->execute();

            $resultGetSchedules = $stmtGetSchedules->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($resultGetSchedules)) {
                return [
                    'status' => 'success',
                    'data' => $resultGetSchedules
                ];
            } else {
                return [
                    'status' => 'success',
                    'data' => 'No hay datos'
                ];
            }

        } catch (PDOException $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function getActiveAppointment(ModifyDatesSchedules $specificDate)
    {
        try {
            $queryGetAppointment = "SELECT *
                                    FROM appointment
                                    WHERE `Date` = :Date";

            $stmtGetAppointment = $this->conn->prepare($queryGetAppointment);
            $stmtGetAppointment->bindParam(':Date', $specificDate->date, PDO::PARAM_STR);
            $stmtGetAppointment->execute();

            $resultGetAppointment = $stmtGetAppointment->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($resultGetAppointment)) {
                return [
                    'status' => 'success',
                    'data' => $resultGetAppointment
                ];
            } else {
                return [
                    'status' => 'success',
                    'data' => 'No hay datos'
                ];
            }

        } catch (PDOException $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function searchAppointmentDates(ModifyDatesSchedules $modifyDatesSchedules)
    {
        try {
            $querySearchAppointments = "SELECT Id, NameLastname
                                        FROM appointment
                                        WHERE `Date` = :Date";

            $stmtSearchAppointments = $this->conn->prepare($querySearchAppointments);
            $stmtSearchAppointments->bindParam(':Date', $modifyDatesSchedules->date, PDO::PARAM_STR);
            $stmtSearchAppointments->execute();

            $resultSearchAppointments = [];

            while ($data = $stmtSearchAppointments->fetch(PDO::FETCH_ASSOC)) {
                $register = [
                    "Id" => $data['Id'],
                    "NameLastname" => $data['NameLastname']
                ];

                $resultSearchAppointments[] = $register;
            }

            if (!empty($resultSearchAppointments)) {
                return [
                    'status' => 'success',
                    'data' => $resultSearchAppointments
                ];
            } else {
                return [
                    'status' => 'success',
                    'message' => 'No hay citas'
                ];
            }

        } catch (PDOException $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function searchAppointmentHourDates(ModifyDatesSchedules $modifyDatesSchedules)
    {
        try {
            $querySearchAppointments = "SELECT Id, NameLastname
                                        FROM appointment
                                        WHERE `Date` = :Date
                                        AND Schedule = :Schedule";

            $stmtSearchAppointments = $this->conn->prepare($querySearchAppointments);
            $stmtSearchAppointments->bindParam(':Date', $modifyDatesSchedules->date, PDO::PARAM_STR);
            $stmtSearchAppointments->bindParam(':Schedule', $modifyDatesSchedules->hour, PDO::PARAM_STR);
            $stmtSearchAppointments->execute();

            $resultSearchAppointments = [];

            while ($data = $stmtSearchAppointments->fetch(PDO::FETCH_ASSOC)) {
                $register = [
                    "Id" => $data['Id'],
                    "NameLastname" => $data['NameLastname']
                ];

                $resultSearchAppointments[] = $register;
            }

            if (!empty($resultSearchAppointments)) {
                return [
                    'status' => 'success',
                    'data' => $resultSearchAppointments
                ];
            } else {
                return [
                    'status' => 'success',
                    'message' => 'No hay citas'
                ];
            }

        } catch (PDOException $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function availableDisableDate(ModifyDatesSchedules $modifyDatesSchedules)
    {
        try {
            $this->conn->beginTransaction();

            $username = $this->username;

            // Buscar si la fecha ya existe
            $queryValidateDate = "SELECT `Date`
                                  FROM calendar_exceptions
                                  WHERE `Date` = :Date
                                  LIMIT 1";

            $stmtValidate = $this->conn->prepare($queryValidateDate);
            $stmtValidate->bindParam(':Date', $modifyDatesSchedules->date, PDO::PARAM_STR);
            $stmtValidate->execute();

            $resultValidateDates = $stmtValidate->fetch(PDO::FETCH_ASSOC);

            if ($resultValidateDates) {

                // Actualizar el registro existente
                $queryUpdateRegister = "UPDATE calendar_exceptions
                                        SET StatusDate = :StatusDate,
                                            LastEditBy = :LastEditBy
                                        WHERE `Date` = :Date";

                $stmtUpdateRegister = $this->conn->prepare($queryUpdateRegister);
                $stmtUpdateRegister->bindParam(':StatusDate', $modifyDatesSchedules->statusDate, PDO::PARAM_STR);
                $stmtUpdateRegister->bindParam(':LastEditBy', $username, PDO::PARAM_STR);
                $stmtUpdateRegister->bindParam(':Date', $modifyDatesSchedules->date, PDO::PARAM_STR);
                $stmtUpdateRegister->execute();

            } else {

                // Insertar un nuevo registro
                $queryInsertRegister = "INSERT INTO calendar_exceptions (
                                            `Date`,
                                            StatusDate,
                                            LastEditBy,
                                            LastEditDt
                                        )
                                        VALUES (
                                            :Date,
                                            :StatusDate,
                                            :LastEditBy,
                                            NOW()
                                        )";

                $stmtInsertRegister = $this->conn->prepare($queryInsertRegister);
                $stmtInsertRegister->bindParam(':Date', $modifyDatesSchedules->date, PDO::PARAM_STR);
                $stmtInsertRegister->bindParam(':StatusDate', $modifyDatesSchedules->statusDate, PDO::PARAM_STR);
                $stmtInsertRegister->bindParam(':LastEditBy', $username, PDO::PARAM_STR);
                $stmtInsertRegister->execute();
            }

            $this->conn->commit();

            return [
                'status' => 'success',
                'message' => 'Fecha actualizada'
            ];

        } catch (PDOException $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function availableDisableHour(ModifyDatesSchedules $modifyDatesSchedules)
    {
        try {
            $this->conn->beginTransaction();

            $username = $this->username;

            // Buscar si la fecha y hora ya existen
            $queryValidateDate = "SELECT `Date`
                                  FROM calendar_exceptions
                                  WHERE `Date` = :Date
                                  AND `Hour` = :Hour
                                  LIMIT 1";

            $stmtValidateDate = $this->conn->prepare($queryValidateDate);
            $stmtValidateDate->bindParam(':Date', $modifyDatesSchedules->date, PDO::PARAM_STR);
            $stmtValidateDate->bindParam(':Hour', $modifyDatesSchedules->hour, PDO::PARAM_STR);
            $stmtValidateDate->execute();

            $resultValidateDates = $stmtValidateDate->fetch(PDO::FETCH_ASSOC);

            if ($resultValidateDates) {

                // Actualizar el registro existente
                $queryUpdateRegister = "UPDATE calendar_exceptions
                                        SET StatusHour = :StatusHour,
                                            LastEditBy = :LastEditBy
                                        WHERE `Date` = :Date
                                        AND `Hour` = :Hour
                                        LIMIT 1";

                $stmtUpdateRegister = $this->conn->prepare($queryUpdateRegister);
                $stmtUpdateRegister->bindParam(':Date', $modifyDatesSchedules->date, PDO::PARAM_STR);
                $stmtUpdateRegister->bindParam(':StatusHour', $modifyDatesSchedules->statusHour, PDO::PARAM_STR);
                $stmtUpdateRegister->bindParam(':Hour', $modifyDatesSchedules->hour, PDO::PARAM_STR);
                $stmtUpdateRegister->bindParam(':LastEditBy', $username, PDO::PARAM_STR);
                $stmtUpdateRegister->execute();

            } else {

                // Insertar un nuevo registro
                $queryInsertRegister = "INSERT INTO calendar_exceptions (
                                            `Date`,
                                            StatusDate,
                                            `Hour`,
                                            StatusHour,
                                            LastEditBy,
                                            LastEditDt
                                        )
                                        VALUES (
                                            :Date,
                                            :StatusDate,
                                            :Hour,
                                            :StatusHour,
                                            :LastEditBy,
                                            NOW()
                                        )";

                $stmtInsertRegister = $this->conn->prepare($queryInsertRegister);
                $stmtInsertRegister->bindParam(':Date', $modifyDatesSchedules->date, PDO::PARAM_STR);
                $stmtInsertRegister->bindParam(':StatusDate', $modifyDatesSchedules->statusDate, PDO::PARAM_STR);
                $stmtInsertRegister->bindParam(':Hour', $modifyDatesSchedules->hour, PDO::PARAM_STR);
                $stmtInsertRegister->bindParam(':StatusHour', $modifyDatesSchedules->statusHour, PDO::PARAM_STR);
                $stmtInsertRegister->bindParam(':LastEditBy', $username, PDO::PARAM_STR);
                $stmtInsertRegister->execute();
            }

            $this->conn->commit();

            return [
                'status' => 'success',
                'message' => 'Hora actualizada'
            ];

        } catch (PDOException $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
