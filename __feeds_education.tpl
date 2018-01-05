
                                    {if $education['University']}
                                 
                                            <li>
                                                <div class="about-list-item">
                                              {$education['University']}
                                                </div>
                                            </li>
                          
                                    {/if}

                                    {if $education['MacroSubject']}
                                        
                                            <li>
                                                <div class="about-list-item">
                                                   
                                                   {$education['MacroSubject']} 
                                                   
                                                </div>
                                            </li>
                                      
                                    {/if}


                                    {if $education['Grade']}
                                       
                                            <li>
                                                <div class="about-list-item">
                                                    
                                                  {$education['Grade']}

                                                </div>
                                            </li>
      
                                    {/if}

                                    {if $education['Country']}
                                       
                                            <li>
                                                <div class="about-list-item">
                                             {__("Pais: ")}       <span class="text-link">{$education['Country']}</span>
                                                </div>
                                            </li>
      
                                    {/if}

                                    {if $education['Idioma']}
                                       
                                            <li>
                                                <div class="about-list-item">
                                              {__("Idioma Carrera: ")} {$education['Idioma']}
                                                </div>
                                            </li>
      
                                    {/if}

                                  {if $education['CompletionTime']}
                                       
                                            <li>
                                                <div class="about-list-item">
                                               {__("Tiempo de Completamiento: ")}{$education['CompletionTime']} {__(" años")}
                                                </div>
                                            </li>
      
                                    {/if}

                                {if $education['AverageExams_Mark']}
                                       
                                            <li>
                                                <div class="about-list-item">
                                               {__("Promedio Examenes: ")}{$education['AverageExams_Mark']} 
                                                </div>
                                            </li>
      
                                    {/if}


                                  {if $education['EndDate']}
                                       
                                            <li>
                                                <div class="about-list-item">
                                               {$education['StartYear']} {__(" - ")}  {$education['EndYear']}
                                                </div>
                                            </li>
                                      {else}

                                      <li>
                                                <div class="about-list-item">
                                               {$education['StartYear']} {__(" -  Actual")}
                                                </div>
                                        </li>
                                    {/if}