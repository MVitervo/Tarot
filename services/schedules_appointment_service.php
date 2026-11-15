
<?php

class SchedulesService
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

    public function schedules()
    {
        try {

            $querySchedules = "SELECT * FROM Schedules";

            $stmtSchedules = $this->conn->prepare($querySchedules);
            $stmtSchedules->execute();

            $resultSchedules = $stmtSchedules->fetchAll(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'data' => $resultSchedules
            ];
        } catch (PDOException $e) {

            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function saveAppointment(Appointment $appointment)
    {
        try {

            $this->conn->beginTransaction();

            $username = $this->username;

            // Validar que la fecha y el horario no estén ocupados
            $queryValidationAppointment = "SELECT Id
                                           FROM appointment
                                           WHERE `Date` = :date
                                           AND Schedule = :Schedule
                                           LIMIT 1";

            $stmtValidationAppointment = $this->conn->prepare($queryValidationAppointment);
            $stmtValidationAppointment->bindParam(':date', $appointment->date, PDO::PARAM_STR);
            $stmtValidationAppointment->bindParam(':Schedule', $appointment->schedule, PDO::PARAM_STR);
            $stmtValidationAppointment->execute();

            $resultValidationAppointment = $stmtValidationAppointment->fetch(PDO::FETCH_ASSOC);

            if ($resultValidationAppointment) {
                throw new Exception('No es posible hacer la cita, esta fecha y hora ya están ocupadas');
            }

            // Insertar la cita
            $querySaveAppointment = "INSERT INTO appointment (
                                        NameLastname,
                                        ZodiacSign,
                                        Nationality,
                                        Email,
                                        Phone,
                                        `Date`,
                                        Schedule,
                                        LastEditBy,
                                        LastEditDt
                                    )
                                    VALUES (
                                        :NameLastname,
                                        :zodiacSign,
                                        :nationality,
                                        :Email,
                                        :Phone,
                                        :Date,
                                        :Schedule,
                                        :LastEditBy,
                                        NOW()
                                    )";

            $stmtSaveAppointment = $this->conn->prepare($querySaveAppointment);

            $stmtSaveAppointment->bindParam(':NameLastname', $appointment->nameLastname, PDO::PARAM_STR);
            $stmtSaveAppointment->bindParam(':zodiacSign', $appointment->zodiacSign, PDO::PARAM_STR);
            $stmtSaveAppointment->bindParam(':nationality', $appointment->nationality, PDO::PARAM_STR);
            $stmtSaveAppointment->bindParam(':Email', $appointment->email, PDO::PARAM_STR);
            $stmtSaveAppointment->bindParam(':Phone', $appointment->phone, PDO::PARAM_STR);
            $stmtSaveAppointment->bindParam(':Date', $appointment->date, PDO::PARAM_STR);
            $stmtSaveAppointment->bindParam(':Schedule', $appointment->schedule, PDO::PARAM_STR);
            $stmtSaveAppointment->bindParam(':LastEditBy', $username, PDO::PARAM_STR);

            $stmtSaveAppointment->execute();

            $this->conn->commit();

            return [
                'status' => 'success',
                'message' => 'Cita agendada con éxito! Te contactará el número 656 102 9683'
            ];
        } catch (Exception $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        } catch (PDOException $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            return [
                'status' => 'error',
                'message' => "Error de base de datos " . $e->getMessage()
            ];
        }
    }
}
