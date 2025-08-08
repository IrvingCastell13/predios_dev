<template>
  <AppLayout clases="flex-fill">
    <!-- <pre>{{ store.html }}</pre> -->
    <div class="white-box px-3 py-2 h-full d-flex flex-column">
      <div class="py-2">
        <div class="row g-3 align-items-center">
          <div class="col-auto">
            <a :href="`/admin/planMantenimientoDetalle/${id}`">
              <span class="flex-shrink-0">
                <svg
                  width="16"
                  height="14"
                  viewBox="0 0 16 14"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M1.58301 7H14.4163M1.58301 7L7.08301 12.5M1.58301 7L7.08301 1.5"
                    stroke="#0F6491"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
              </span>
            </a>
          </div>
          <div class="col-auto">
            <img src="/images/shape-icon-1.svg" alt="" />
          </div>
          <div class="col">
            <div class="row g-2 align-items-center">
              <div class="col">
                <div>
                  <h6 class="fw-bold primary">
                    {{ store?.plan?.NombrePlan }} [ID-{{
                      store?.plan?.ClavePlan
                    }}]
                  </h6>
                </div>
              </div>
              <div class="col-sm-auto">
                <div class="row g-3">
                  <div class="col-auto">
                    <select
                      class="form-control"
                      v-model="store.filtroVer"
                      @change="store.getTablero(id)"
                    >
                      <option value="">Ver: Todos</option>
                      <option v-for="item in ver" :value="item.value">
                        {{ item.label }}
                      </option>
                    </select>
                  </div>
                  <div class="col-auto">
                    <select class="form-control">
                      <option>Responsable: UserName</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="pb-2 flex-fill d-flex overflow-y-auto">
        <div class="row g-2 flex-fill">
          <div v-for="(item, key) in store.html" class="col-lg-4 d-flex">
            <div class="border-radius-10 p-2 bg-light-grey-2 flex-fill">
              <div>
                <p class="fw-semibold">{{ key }}</p>
              </div>
              <div class="mt-2">
                <div v-for="card in item" class="border-box p-2 bg-white">
                  <div class="row" v-if="!card.IDOrdenTrabajo">
                    <div class="col">
                      <div>
                        <div class="devider-row flex-wrap">
                          <div class="devider-column">
                            <div class="d-flex align-items-center gap-2">
                              <p class="fs-sm fw-medium primary">
                                {{ card.fechas_rango }}
                              </p>
                              <img src="/images/calendar-icon-xs.svg" alt="" />
                            </div>
                          </div>
                          <div class="devider-column">
                            <div
                              class="d-flex flex-wrap align-items-centercenter gap-5px"
                            >
                              <span class="table-badge info">
                                <svg
                                  width="12"
                                  height="16"
                                  viewBox="0 0 12 16"
                                  fill="none"
                                  xmlns="http://www.w3.org/2000/svg"
                                >
                                  <path
                                    d="M6 0.5C6.74168 0.5 7.4667 0.719934 8.08339 1.13199C8.70007 1.54404 9.18072 2.12971 9.46455 2.81494C9.74838 3.50016 9.82264 4.25416 9.67795 4.98159C9.53325 5.70902 9.1761 6.3772 8.65165 6.90165C8.1272 7.4261 7.45902 7.78325 6.73159 7.92795C6.00416 8.07264 5.25016 7.99838 4.56494 7.71455C3.87971 7.43072 3.29404 6.95007 2.88199 6.33339C2.46993 5.7167 2.25 4.99168 2.25 4.25L2.25375 4.08725C2.29569 3.12181 2.70871 2.20983 3.40667 1.54149C4.10463 0.873156 5.03365 0.500056 6 0.5ZM7.5 9.5C8.49456 9.5 9.44839 9.89509 10.1517 10.5983C10.8549 11.3016 11.25 12.2554 11.25 13.25V14C11.25 14.3978 11.092 14.7794 10.8107 15.0607C10.5294 15.342 10.1478 15.5 9.75 15.5H2.25C1.85218 15.5 1.47064 15.342 1.18934 15.0607C0.908035 14.7794 0.75 14.3978 0.75 14V13.25C0.75 12.2554 1.14509 11.3016 1.84835 10.5983C2.55161 9.89509 3.50544 9.5 4.5 9.5H7.5Z"
                                    fill="#B38F00"
                                  />
                                </svg>
                                No asignado
                              </span>
                              <span class="table-badge info">Programada</span>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div
                        class="d-flex flex-wrap align-items-centercenter gap-5px mt-2"
                      >
                        <span class="table-badge">
                          <svg
                            width="23"
                            height="24"
                            viewBox="0 0 23 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                          >
                            <path
                              d="M14.3753 5.87496L7.06278 13.1875C6.61522 13.635 6.36379 14.242 6.36379 14.875C6.36379 15.5079 6.61522 16.1149 7.06278 16.5625C7.51033 17.01 8.11734 17.2614 8.75028 17.2614C9.38321 17.2614 9.99022 17.01 10.4378 16.5625L17.7503 9.24996C18.6454 8.35485 19.1482 7.14083 19.1482 5.87496C19.1482 4.60909 18.6454 3.39507 17.7503 2.49996C16.8552 1.60485 15.6411 1.10199 14.3753 1.10199C13.1094 1.10199 11.8954 1.60485 11.0003 2.49996L3.68778 9.81246C2.34512 11.1551 1.59082 12.9762 1.59082 14.875C1.59082 16.7738 2.34512 18.5948 3.68778 19.9375C5.03043 21.2801 6.85147 22.0344 8.75028 22.0344C10.6491 22.0344 12.4701 21.2801 13.8128 19.9375L21.1253 12.625"
                              stroke="#00A488"
                              stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                            ></path>
                          </svg>
                        </span>
                        <span class="table-badge"
                          >Documento: {{ card.equipo.DescripcionEquipo }}</span
                        >
                        <span class="table-badge"> {{ card.accion }}</span>
                      </div>
                      <div
                        class="d-flex flex-wrap align-items-centercenter gap-5px mt-2"
                      >
                        <span class="table-badge"
                          ><span class="danger"
                            >Tareas cumplidas: 0/{{
                              card.cantidad_tareas
                            }}</span
                          ></span
                        >
                        <span class="table-badge">Tiempo: 2h / - -</span>
                        <div
                          class="flex-fill d-flex align-items-center gap-10 justify-content-end"
                        >
                          <span class="bg-sky-blue-light px-1 border-radius-4">
                            <svg
                              width="17"
                              height="15"
                              viewBox="0 0 17 15"
                              fill="none"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <path
                                d="M8 10.5C7.72667 10.5 7.45667 10.4858 7.19167 10.4583C4.54833 10.1817 2.31667 8.52917 0.5 5.5C2.5 2.16667 5 0.5 8 0.5C10.8875 0.5 13.3117 2.04417 15.2725 5.13167M14.8335 12.3333L16.3335 13.8333M6.33333 5.5C6.33333 5.94203 6.50893 6.36595 6.82149 6.67851C7.13405 6.99107 7.55797 7.16667 8 7.16667C8.44203 7.16667 8.86595 6.99107 9.17851 6.67851C9.49107 6.36595 9.66667 5.94203 9.66667 5.5C9.66667 5.05797 9.49107 4.63405 9.17851 4.32149C8.86595 4.00893 8.44203 3.83333 8 3.83333C7.55797 3.83333 7.13405 4.00893 6.82149 4.32149C6.50893 4.63405 6.33333 5.05797 6.33333 5.5ZM10.5 10.5C10.5 11.163 10.7634 11.7989 11.2322 12.2678C11.7011 12.7366 12.337 13 13 13C13.663 13 14.2989 12.7366 14.7678 12.2678C15.2366 11.7989 15.5 11.163 15.5 10.5C15.5 9.83696 15.2366 9.20107 14.7678 8.73223C14.2989 8.26339 13.663 8 13 8C12.337 8 11.7011 8.26339 11.2322 8.73223C10.7634 9.20107 10.5 9.83696 10.5 10.5Z"
                                stroke="#0F6491"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                              ></path>
                            </svg>
                          </span>
                          <svg
                            v-if="card.follow"
                            @click="DeleteFollow(card.follow)"
                            class="cursor-pointer"
                            width="24"
                            height="23"
                            viewBox="0 0 24 23"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                          >
                            <path
                              d="M8.625 17.125V18.25C8.625 19.1451 8.98058 20.0036 9.61351 20.6365C10.2464 21.2694 11.1049 21.625 12 21.625C12.8951 21.625 13.7536 21.2694 14.3865 20.6365C15.0194 20.0036 15.375 19.1451 15.375 18.25V17.125M22.1251 5.56788C21.3871 3.96278 20.3156 2.53341 18.9818 1.375M1.875 5.56788C2.61227 3.963 3.68304 2.53363 5.016 1.375M9.75 3.625C9.75 3.02826 9.98705 2.45597 10.409 2.03401C10.831 1.61205 11.4033 1.375 12 1.375C12.5967 1.375 13.169 1.61205 13.591 2.03401C14.0129 2.45597 14.25 3.02826 14.25 3.625C15.542 4.23591 16.6433 5.18687 17.4361 6.37596C18.2288 7.56506 18.683 8.94746 18.75 10.375V13.75C18.8347 14.4494 19.0824 15.1192 19.4732 15.7054C19.864 16.2916 20.3869 16.7778 21 17.125H3C3.61306 16.7778 4.13604 16.2916 4.52684 15.7054C4.91764 15.1192 5.16534 14.4494 5.25 13.75V10.375C5.31701 8.94746 5.77121 7.56506 6.56394 6.37596C7.35667 5.18687 8.45804 4.23591 9.75 3.625Z"
                              stroke="#B38F00"
                              stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                            />
                          </svg>
                          <svg
                            v-else
                            @click="follow(card.IDAccionesMantenimiento)"
                            class="cursor-pointer"
                            width="24"
                            height="23"
                            viewBox="0 0 24 23"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                          >
                            <path
                              d="M8.625 17.125V18.25C8.625 19.1451 8.98058 20.0036 9.61351 20.6365C10.2464 21.2694 11.1049 21.625 12 21.625C12.8951 21.625 13.7536 21.2694 14.3865 20.6365C15.0194 20.0036 15.375 19.1451 15.375 18.25V17.125M22.1251 5.56788C21.3871 3.96278 20.3156 2.53341 18.9818 1.375M1.875 5.56788C2.61227 3.963 3.68304 2.53363 5.016 1.375M9.75 3.625C9.75 3.02826 9.98705 2.45597 10.409 2.03401C10.831 1.61205 11.4033 1.375 12 1.375C12.5967 1.375 13.169 1.61205 13.591 2.03401C14.0129 2.45597 14.25 3.02826 14.25 3.625C15.542 4.23591 16.6433 5.18687 17.4361 6.37596C18.2288 7.56506 18.683 8.94746 18.75 10.375V13.75C18.8347 14.4494 19.0824 15.1192 19.4732 15.7054C19.864 16.2916 20.3869 16.7778 21 17.125H3C3.61306 16.7778 4.13604 16.2916 4.52684 15.7054C4.91764 15.1192 5.16534 14.4494 5.25 13.75V10.375C5.31701 8.94746 5.77121 7.56506 6.56394 6.37596C7.35667 5.18687 8.45804 4.23591 9.75 3.625Z"
                              stroke="#C0C0C0"
                              stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                            />
                          </svg>
                        </div>
                      </div>
                      <div class="mt-2">
                        <p
                          class="fs-xs fw-semibold sky-blue cursor-pointer"
                          @click="crearOrdenTrabajo(card)"
                        >
                          Crear Orden de trabajo
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                <!--
                                <div class="border-box p-2 bg-white mt-2">
                                    <div class="row">
                                        <div class="col">
                                            <div>
                                                <div class="devider-row flex-wrap">
                                                    <div class="devider-column">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <p class="fs-sm fw-medium primary">22/07 - 31/07</p>
                                                            <img src="/images/calendar-icon-xs.svg" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="devider-column">
                                                        <div
                                                            class="d-flex flex-wrap align-items-centercenter gap-5px">
                                                            <span class="table-badge info">
                                                                <svg width="12" height="16" viewBox="0 0 12 16"
                                                                    fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M6 0.5C6.74168 0.5 7.4667 0.719934 8.08339 1.13199C8.70007 1.54404 9.18072 2.12971 9.46455 2.81494C9.74838 3.50016 9.82264 4.25416 9.67795 4.98159C9.53325 5.70902 9.1761 6.3772 8.65165 6.90165C8.1272 7.4261 7.45902 7.78325 6.73159 7.92795C6.00416 8.07264 5.25016 7.99838 4.56494 7.71455C3.87971 7.43072 3.29404 6.95007 2.88199 6.33339C2.46993 5.7167 2.25 4.99168 2.25 4.25L2.25375 4.08725C2.29569 3.12181 2.70871 2.20983 3.40667 1.54149C4.10463 0.873156 5.03365 0.500056 6 0.5ZM7.5 9.5C8.49456 9.5 9.44839 9.89509 10.1517 10.5983C10.8549 11.3016 11.25 12.2554 11.25 13.25V14C11.25 14.3978 11.092 14.7794 10.8107 15.0607C10.5294 15.342 10.1478 15.5 9.75 15.5H2.25C1.85218 15.5 1.47064 15.342 1.18934 15.0607C0.908035 14.7794 0.75 14.3978 0.75 14V13.25C0.75 12.2554 1.14509 11.3016 1.84835 10.5983C2.55161 9.89509 3.50544 9.5 4.5 9.5H7.5Z"
                                                                        fill="#B38F00" />
                                                                </svg>
                                                                No asignado
                                                            </span>
                                                            <span class="table-badge info">Programada</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-centercenter gap-5px mt-2">
                                                <span class="table-badge">
                                                    <svg width="23" height="24" viewBox="0 0 23 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M14.3753 5.87496L7.06278 13.1875C6.61522 13.635 6.36379 14.242 6.36379 14.875C6.36379 15.5079 6.61522 16.1149 7.06278 16.5625C7.51033 17.01 8.11734 17.2614 8.75028 17.2614C9.38321 17.2614 9.99022 17.01 10.4378 16.5625L17.7503 9.24996C18.6454 8.35485 19.1482 7.14083 19.1482 5.87496C19.1482 4.60909 18.6454 3.39507 17.7503 2.49996C16.8552 1.60485 15.6411 1.10199 14.3753 1.10199C13.1094 1.10199 11.8954 1.60485 11.0003 2.49996L3.68778 9.81246C2.34512 11.1551 1.59082 12.9762 1.59082 14.875C1.59082 16.7738 2.34512 18.5948 3.68778 19.9375C5.03043 21.2801 6.85147 22.0344 8.75028 22.0344C10.6491 22.0344 12.4701 21.2801 13.8128 19.9375L21.1253 12.625"
                                                            stroke="#00A488" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                    </svg>
                                                </span>
                                                <span class="table-badge">Sin evidencias</span>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-centercenter gap-5px mt-2">
                                                <span class="table-badge"><span class="danger">Tareas cumplidas:
                                                        0/12</span></span>
                                                <span class="table-badge">Tiempo: 2h / - -</span>
                                                <div class="flex-fill d-flex align-items-center gap-10 justify-content-end">
                                                    <span class="bg-sky-blue-light px-1 border-radius-4">
                                                        <svg width="17" height="15" viewBox="0 0 17 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M8 10.5C7.72667 10.5 7.45667 10.4858 7.19167 10.4583C4.54833 10.1817 2.31667 8.52917 0.5 5.5C2.5 2.16667 5 0.5 8 0.5C10.8875 0.5 13.3117 2.04417 15.2725 5.13167M14.8335 12.3333L16.3335 13.8333M6.33333 5.5C6.33333 5.94203 6.50893 6.36595 6.82149 6.67851C7.13405 6.99107 7.55797 7.16667 8 7.16667C8.44203 7.16667 8.86595 6.99107 9.17851 6.67851C9.49107 6.36595 9.66667 5.94203 9.66667 5.5C9.66667 5.05797 9.49107 4.63405 9.17851 4.32149C8.86595 4.00893 8.44203 3.83333 8 3.83333C7.55797 3.83333 7.13405 4.00893 6.82149 4.32149C6.50893 4.63405 6.33333 5.05797 6.33333 5.5ZM10.5 10.5C10.5 11.163 10.7634 11.7989 11.2322 12.2678C11.7011 12.7366 12.337 13 13 13C13.663 13 14.2989 12.7366 14.7678 12.2678C15.2366 11.7989 15.5 11.163 15.5 10.5C15.5 9.83696 15.2366 9.20107 14.7678 8.73223C14.2989 8.26339 13.663 8 13 8C12.337 8 11.7011 8.26339 11.2322 8.73223C10.7634 9.20107 10.5 9.83696 10.5 10.5Z" stroke="#0F6491" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </svg>
                                                    </span>
                                                    <span><img src="/images/bell-icon.svg" alt=""></span>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <p class="fs-xs fw-semibold sky-blue">Crear Orden de trabajo</p>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
              </div>
            </div>
          </div>
          <!-- <div class="col-lg-4 d-flex">
                        <div class="border-radius-10 p-2 bg-light-grey-2 flex-fill">
                            <div>
                                <p class="fw-semibold sky-blue">Asignado</p>
                            </div>
                            <div class="mt-2">
                                <div class="border-box p-2 bg-white">
                                    <div class="row">
                                        <div class="col">
                                            <div>
                                                <div class="devider-row flex-wrap">
                                                    <div class="devider-column">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <p class="fs-sm fw-medium primary">22/07 - 31/07</p>
                                                            <img src="/images/calendar-icon-xs.svg" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="devider-column">
                                                        <div class="d-inline-flex align-items-center gap-5px">
                                                            <div class="users"><img src="/images/user-image.jpg" alt=""></div>
                                                            <p class="fs-sm fw-medium">
                                                                NameExample
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="devider-column">
                                                        <div>
                                                            <p class="fs-sm fw-medium"><span class="fs fw-semibold">OT.01</span>20240710001</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-centercenter gap-5px mt-2">
                                                <span class="table-badge success">Asignada</span>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-centercenter gap-5px mt-2">
                                                <span class="table-badge purple">
                                                    <svg width="23" height="24" viewBox="0 0 23 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M14.3753 5.87496L7.06278 13.1875C6.61522 13.635 6.36379 14.242 6.36379 14.875C6.36379 15.5079 6.61522 16.1149 7.06278 16.5625C7.51033 17.01 8.11734 17.2614 8.75028 17.2614C9.38321 17.2614 9.99022 17.01 10.4378 16.5625L17.7503 9.24996C18.6454 8.35485 19.1482 7.14083 19.1482 5.87496C19.1482 4.60909 18.6454 3.39507 17.7503 2.49996C16.8552 1.60485 15.6411 1.10199 14.3753 1.10199C13.1094 1.10199 11.8954 1.60485 11.0003 2.49996L3.68778 9.81246C2.34512 11.1551 1.59082 12.9762 1.59082 14.875C1.59082 16.7738 2.34512 18.5948 3.68778 19.9375C5.03043 21.2801 6.85147 22.0344 8.75028 22.0344C10.6491 22.0344 12.4701 21.2801 13.8128 19.9375L21.1253 12.625"
                                                            stroke="#00A488" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                    </svg>
                                                </span>
                                                <span class="table-badge sky-blue">7 Evidencias adjuntas</span>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-centercenter gap-5px mt-2">
                                                <span class="table-badge success">Tareas cumplidas: 0/12</span>
                                                <span class="table-badge">Tiempo: 2h / - -</span>
                                                <div class="flex-fill d-flex align-items-center gap-10 justify-content-end">
                                                    <span class="bg-sky-blue-light px-1 border-radius-4">
                                                        <svg width="17" height="15" viewBox="0 0 17 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M8 10.5C7.72667 10.5 7.45667 10.4858 7.19167 10.4583C4.54833 10.1817 2.31667 8.52917 0.5 5.5C2.5 2.16667 5 0.5 8 0.5C10.8875 0.5 13.3117 2.04417 15.2725 5.13167M14.8335 12.3333L16.3335 13.8333M6.33333 5.5C6.33333 5.94203 6.50893 6.36595 6.82149 6.67851C7.13405 6.99107 7.55797 7.16667 8 7.16667C8.44203 7.16667 8.86595 6.99107 9.17851 6.67851C9.49107 6.36595 9.66667 5.94203 9.66667 5.5C9.66667 5.05797 9.49107 4.63405 9.17851 4.32149C8.86595 4.00893 8.44203 3.83333 8 3.83333C7.55797 3.83333 7.13405 4.00893 6.82149 4.32149C6.50893 4.63405 6.33333 5.05797 6.33333 5.5ZM10.5 10.5C10.5 11.163 10.7634 11.7989 11.2322 12.2678C11.7011 12.7366 12.337 13 13 13C13.663 13 14.2989 12.7366 14.7678 12.2678C15.2366 11.7989 15.5 11.163 15.5 10.5C15.5 9.83696 15.2366 9.20107 14.7678 8.73223C14.2989 8.26339 13.663 8 13 8C12.337 8 11.7011 8.26339 11.2322 8.73223C10.7634 9.20107 10.5 9.83696 10.5 10.5Z" stroke="#0F6491" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </svg>
                                                    </span>
                                                    <span><img src="/images/bell-icon.svg" alt=""></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 d-flex">
                        <div class="border-radius-10 p-2 bg-light-grey-2 flex-fill">
                            <div>
                                <p class="fw-semibold success">Realizado</p>
                            </div>
                            <div class="mt-2">
                                <div class="border-box p-2 bg-white">
                                    <div class="row">
                                        <div class="col">
                                            <div>
                                                <div class="devider-row flex-wrap">
                                                    <div class="devider-column">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <p class="fs-sm fw-medium primary">22/07 - 31/07</p>
                                                            <img src="/images/calendar-icon-xs.svg" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="devider-column">
                                                        <div class="d-inline-flex align-items-center gap-5px">
                                                            <div class="users"><img src="/images/user-image.jpg" alt=""></div>
                                                            <p class="fs-sm fw-medium">
                                                                NameExample
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="devider-column">
                                                        <div>
                                                            <p class="fs-sm fw-medium"><span class="fs fw-semibold">OT.01</span>20240710001</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-centercenter gap-5px mt-2">
                                                <span class="table-badge success">Asignada</span>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-centercenter gap-5px mt-2">
                                                <span class="table-badge purple">
                                                    <svg width="23" height="24" viewBox="0 0 23 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M14.3753 5.87496L7.06278 13.1875C6.61522 13.635 6.36379 14.242 6.36379 14.875C6.36379 15.5079 6.61522 16.1149 7.06278 16.5625C7.51033 17.01 8.11734 17.2614 8.75028 17.2614C9.38321 17.2614 9.99022 17.01 10.4378 16.5625L17.7503 9.24996C18.6454 8.35485 19.1482 7.14083 19.1482 5.87496C19.1482 4.60909 18.6454 3.39507 17.7503 2.49996C16.8552 1.60485 15.6411 1.10199 14.3753 1.10199C13.1094 1.10199 11.8954 1.60485 11.0003 2.49996L3.68778 9.81246C2.34512 11.1551 1.59082 12.9762 1.59082 14.875C1.59082 16.7738 2.34512 18.5948 3.68778 19.9375C5.03043 21.2801 6.85147 22.0344 8.75028 22.0344C10.6491 22.0344 12.4701 21.2801 13.8128 19.9375L21.1253 12.625"
                                                            stroke="#00A488" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                    </svg>
                                                </span>
                                                <span class="table-badge sky-blue">7 Evidencias adjuntas</span>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-centercenter gap-5px mt-2">
                                                <span class="table-badge success">Tareas cumplidas: 0/12</span>
                                                <span class="table-badge">Tiempo: 2h / - -</span>
                                                <div class="flex-fill d-flex align-items-center gap-10 justify-content-end">
                                                    <span class="bg-sky-blue-light px-1 border-radius-4">
                                                        <svg width="17" height="15" viewBox="0 0 17 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M8 10.5C7.72667 10.5 7.45667 10.4858 7.19167 10.4583C4.54833 10.1817 2.31667 8.52917 0.5 5.5C2.5 2.16667 5 0.5 8 0.5C10.8875 0.5 13.3117 2.04417 15.2725 5.13167M14.8335 12.3333L16.3335 13.8333M6.33333 5.5C6.33333 5.94203 6.50893 6.36595 6.82149 6.67851C7.13405 6.99107 7.55797 7.16667 8 7.16667C8.44203 7.16667 8.86595 6.99107 9.17851 6.67851C9.49107 6.36595 9.66667 5.94203 9.66667 5.5C9.66667 5.05797 9.49107 4.63405 9.17851 4.32149C8.86595 4.00893 8.44203 3.83333 8 3.83333C7.55797 3.83333 7.13405 4.00893 6.82149 4.32149C6.50893 4.63405 6.33333 5.05797 6.33333 5.5ZM10.5 10.5C10.5 11.163 10.7634 11.7989 11.2322 12.2678C11.7011 12.7366 12.337 13 13 13C13.663 13 14.2989 12.7366 14.7678 12.2678C15.2366 11.7989 15.5 11.163 15.5 10.5C15.5 9.83696 15.2366 9.20107 14.7678 8.73223C14.2989 8.26339 13.663 8 13 8C12.337 8 11.7011 8.26339 11.2322 8.73223C10.7634 9.20107 10.5 9.83696 10.5 10.5Z" stroke="#0F6491" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </svg>
                                                    </span>
                                                    <span><img src="/images/bell-icon.svg" alt=""></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-box p-2 bg-white mt-2">
                                    <div class="row">
                                        <div class="col">
                                            <div>
                                                <div class="devider-row flex-wrap">
                                                    <div class="devider-column">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <p class="fs-sm fw-medium primary">22/07 - 31/07</p>
                                                            <img src="/images/calendar-icon-xs.svg" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="devider-column">
                                                        <div class="d-inline-flex align-items-center gap-5px">
                                                            <div class="users"><img src="/images/user-image.jpg" alt=""></div>
                                                            <p class="fs-sm fw-medium">
                                                                NameExample
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="devider-column">
                                                        <div>
                                                            <p class="fs-sm fw-medium"><span class="fs fw-semibold">OT.01</span>20240710001</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-centercenter gap-5px mt-2">
                                                <span class="table-badge success">Asignada</span>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-centercenter gap-5px mt-2">
                                                <span class="table-badge purple">
                                                    <svg width="23" height="24" viewBox="0 0 23 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M14.3753 5.87496L7.06278 13.1875C6.61522 13.635 6.36379 14.242 6.36379 14.875C6.36379 15.5079 6.61522 16.1149 7.06278 16.5625C7.51033 17.01 8.11734 17.2614 8.75028 17.2614C9.38321 17.2614 9.99022 17.01 10.4378 16.5625L17.7503 9.24996C18.6454 8.35485 19.1482 7.14083 19.1482 5.87496C19.1482 4.60909 18.6454 3.39507 17.7503 2.49996C16.8552 1.60485 15.6411 1.10199 14.3753 1.10199C13.1094 1.10199 11.8954 1.60485 11.0003 2.49996L3.68778 9.81246C2.34512 11.1551 1.59082 12.9762 1.59082 14.875C1.59082 16.7738 2.34512 18.5948 3.68778 19.9375C5.03043 21.2801 6.85147 22.0344 8.75028 22.0344C10.6491 22.0344 12.4701 21.2801 13.8128 19.9375L21.1253 12.625"
                                                            stroke="#00A488" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                        </path>
                                                    </svg>
                                                </span>
                                                <span class="table-badge sky-blue">7 Evidencias adjuntas</span>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-centercenter gap-5px mt-2">
                                                <span class="table-badge success">Tareas cumplidas: 0/12</span>
                                                <span class="table-badge">Tiempo: 2h / - -</span>
                                                <div class="flex-fill d-flex align-items-center gap-10 justify-content-end">
                                                    <span class="bg-sky-blue-light px-1 border-radius-4">
                                                        <svg width="17" height="15" viewBox="0 0 17 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M8 10.5C7.72667 10.5 7.45667 10.4858 7.19167 10.4583C4.54833 10.1817 2.31667 8.52917 0.5 5.5C2.5 2.16667 5 0.5 8 0.5C10.8875 0.5 13.3117 2.04417 15.2725 5.13167M14.8335 12.3333L16.3335 13.8333M6.33333 5.5C6.33333 5.94203 6.50893 6.36595 6.82149 6.67851C7.13405 6.99107 7.55797 7.16667 8 7.16667C8.44203 7.16667 8.86595 6.99107 9.17851 6.67851C9.49107 6.36595 9.66667 5.94203 9.66667 5.5C9.66667 5.05797 9.49107 4.63405 9.17851 4.32149C8.86595 4.00893 8.44203 3.83333 8 3.83333C7.55797 3.83333 7.13405 4.00893 6.82149 4.32149C6.50893 4.63405 6.33333 5.05797 6.33333 5.5ZM10.5 10.5C10.5 11.163 10.7634 11.7989 11.2322 12.2678C11.7011 12.7366 12.337 13 13 13C13.663 13 14.2989 12.7366 14.7678 12.2678C15.2366 11.7989 15.5 11.163 15.5 10.5C15.5 9.83696 15.2366 9.20107 14.7678 8.73223C14.2989 8.26339 13.663 8 13 8C12.337 8 11.7011 8.26339 11.2322 8.73223C10.7634 9.20107 10.5 9.83696 10.5 10.5Z" stroke="#0F6491" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </svg>
                                                    </span>
                                                    <span><img src="/images/bell-icon.svg" alt=""></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
        </div>
      </div>
    </div>
    <CrearOrdenOt />
  </AppLayout>
</template>

<script setup>
import { onMounted } from "vue";
import AppLayout from "../../../../../Layout/App.vue";
import { usestoreTableroStore } from "./js/storeTablero";
import { abrirModal } from "../../../../../Utilities";
import CrearOrdenOt from "./modales/CrearOrdenOt.vue";
import { ref } from "vue";

const props = defineProps(["id"]);
const store = usestoreTableroStore();

const filtroVer = ref("");

const ver = [
  { label: "6 meses", value: "6 meses" },
  { label: "3 meses", value: "3 meses" },
  { label: "30 dias", value: "30 dias" },
  { label: "7 dias", value: "7 dias" },
];
const crearOrdenTrabajo = (card) => {
  console.log(card);
  store.form.IDAccionesRenovacion = card.IDAccionesRenovacion;
  store.form.tareas = card.tareas;
  store.accionDetalle = card;
  // store.obtenerTipos()
  store.obtenerPersonas();
  store.obtenerProvedores();
  abrirModal("creatOrden");
};

const follow = async (IDObjeto) => {
  try {
    const {
      data: { data },
    } = await axios.post(route("follow.autoFollow"), {
      IDObjeto,
    });

    store.getTablero(props.id);
  } catch (error) {
    console.error(error);
  }
};

const DeleteFollow = async (IDFollow) => {
  try {
    const {
      data: { data },
    } = await axios.delete(route("follow.destroy", IDFollow));

    store.getTablero(props.id);
  } catch (error) {
    console.error(error);
  }
};

onMounted(() => {
  store.id = props.id;
  store.getPlan(props.id);
  store.getTablero(props.id);
});
</script>
