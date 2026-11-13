<?php

class AppointmentSchedulesController
{
    /*
    Nota: la clase SchedulesService la puede encontrar en este punto ya que se importo desde el archivo index.php
    con la instruccion: require './services/schedules_appointment_service.php';
    */
    private SchedulesService $service; // esta es una propiedad tipada
    private AppointmentFactory $appointmentFactory; // esta es una propiedad tipada

    public function __construct(SchedulesService $service, AppointmentFactory $appointmentFactory) // inyeccion de dependencias
    {
        $this->service = $service;
        $this->appointmentFactory = $appointmentFactory;
    }

    public function getSchedules()
    {
        // $date = $_GET['date'] ?? '';

        echo json_encode(
            $this->service->schedules()
        );
    }

    public function saveAppointment()
    {

        $appointment = $this->appointmentFactory->createFromRequest($_POST);

        echo json_encode(
            $this->service->saveAppointment($appointment)
        );
    }

    /* aplican solo para el administrador */

    public function getSchedulesAdmin()
    {
        requireAdmin();

        echo json_encode(
            $this->service->schedules()
        );
    }

    public function saveAppointmentAdmin()
    {
        requireAdmin();

        $appointment = $this->appointmentFactory->createFromRequest($_POST);

        echo json_encode(
            $this->service->saveAppointment($appointment)
        );
    }

    /* aplican solo para el administrador */
}
